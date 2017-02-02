<?php namespace Idmkr\Adwords\Operations\Directors;

use AdCustomizerFeed;
use BatchJobException;

use Idmkr\Adwords\Collections\MutateResultCollection;
use Idmkr\Adwords\Operations\Blueprints\Blueprint;
use Idmkr\Adwords\Operations\Commits\Commit;
use Idmkr\Adwords\Operations\Pipelines\BuildPipeline;
use Idmkr\Adwords\Repositories\Batch\BatchJobRepository;

use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Cartalyst\Support\Traits;

use Operation;
use Exception;
use Storage;

/**
 * Batch Operations Director
 *
 */
class BatchOperationsDirector implements DirectorInterface
{
    use Traits\EventTrait;

    /**
     * @var AdWordsUser
     */
    public $adwordsUser;
    /**
     * @var array
     */
    public $data;

    /** @var  BatchJobRepository */
    protected $adwordsBatchs;

    public function __construct(AdWordsUser $adwordsUser, Array $data)
    {
        $this->adwordsUser = $adwordsUser;
        $this->adwordsBatchs = app('idmkr.adwords.batch');
        $this->data = $data;

        $this->setDispatcher(app('events'));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return AdWordsUser
     */
    public function getAdwordsUser()
    {
        return $this->adwordsUser;
    }

    /**
     * @param $scope
     *
     * @return BuildPipeline
     */
    public function pipeline($scope) : BuildPipeline
    {
        $pipeline = new BuildPipeline($this, $scope);
        return $pipeline;
    }

    /**
     * Preview the results from a blueprint
     * Used for generation preview.
     *
     * @return Collection
     */
    public function blueprintPreview(Blueprint $blueprint) : Collection
    {
        $data = new Collection();

        $key = class_basename($blueprint);
        $pipelines = $blueprint->execute($this);
        $data[$key] = new Collection();
        /** @var BuildPipeline $pipeline */
        foreach ($pipelines as $pipeline) {
            $commits = (new Collection($pipeline->getCommits()))->map(function ($commit) {

                $entity = array_dot(json_decode(json_encode($commit->operation->operand), true));
                return [
                    'data' => array_trim($entity),
                    'type' => class_basename($commit->operation->operand),
                    'operator' => $commit->operation->operator
                ];
            });
            $data[$key][] = [
                'scope' => class_basename($pipeline->scope) . ' ' . $pipeline->scope->id,
                'payload' => array_dot(json_decode(json_encode($pipeline->getPayload()), true)),
                'operations' => $commits
            ];
        }


        return $data;
    }

    /**
     * @param Blueprint $blueprint
     *
     * @return Commit[]
     */
    public function execute($blueprint) : Array
    {
        $commits = [];
        $pipelines =  $blueprint->execute($this);

        printf("Executing blueprint ".class_basename($blueprint)."\n");
        printf("\nBuilding operation commits. Pipelines : ".count($pipelines)."\n");
        foreach ($pipelines as $i => $pipeline) {
            foreach($pipeline->getCommits() as $commit) {
                $commits[] = $commit;
            }
        }

        return $commits;
    }


    /**
     * @param array $commits
     *
     * @return MutateResultCollection
     * @throws Exception
     */
    public function upload(Array $commits) : MutateResultCollection
    {
        try {
            $operations = [];
            foreach ($commits as $i => $commit) {
                $operations[] = $commit->operation;
            }

            $operations_count = count($operations);

            // Nothing to upload
            if (!$operations_count) {
                printf("Nothing to upload. Aborting.");
                $this->updateState('upload.abort', ['status' => 'EMPTY']);
            }
            // Business is up
            else {
                printf("Uploading %d operations\n", $operations_count);
                $batchJob = $this->adwordsBatchs->uploadOperations($this->adwordsUser, $operations);

                $this->updateState('upload.success', [
                    'status' => 'POLLING',
                    'adwords_batch_job_id' => $batchJob->id,
                    'uploadUrl' => $batchJob->uploadUrl->url
                ]);

                $this->storeOperations($batchJob->id, $operations);

                printf("\nUploaded %d operations for batch job with ID %d.\n",
                    $operations_count, $batchJob->id);

                // Calculating the ideal sleep seconds delay
                $sleepSeconds = min(60, max(30, round($operations_count/300)));

                return $this->downloadResults($batchJob->uploadUrl->url, $batchJob->id, $sleepSeconds);
            }
        } catch (BatchJobException $e) {
            $this->throwDownloadFailError($e);
        } catch (\SoapFault $e) {
            $this->throwUploadFailError($e);
        } catch (Exception $e) {
            $this->throwUploadFailError($e);
        }

        return new MutateResultCollection();
    }

    /**
     * @param $uploadUrl
     * @param $batchJobId
     *
     * @return array
     * @throws BatchJobException
     */
    protected function downloadResults($uploadUrl, $batchJobId, $sleepSeconds) : MutateResultCollection
    {
        $this->updateState('download.polling', [
            'status' => "ACTIVE",
            'operations_count' => 0,
            'completion_percentage' => 0
        ]);

        $batchJob = $this->adwordsBatchs->poll(
            $this->adwordsUser,
            $batchJobId,
            $sleepSeconds,
            function ($status, $operations_count, $completion_percentage) {
                $this->updateState('download.polling', [
                    'status' => $status,
                    'operations_count' => $operations_count,
                    'completion_percentage' => $completion_percentage
                ]);
            }
        );

        if ($batchJob->processingErrors !== null) {
            $errorMsg = '';
            foreach($batchJob->processingErrors as $error) {
                $errorMsg .= $error->errorString.' (trigger: '.$error->trigger.'). ';
            }
            printf("Processing errors found : %s", $errorMsg);
        } else {
            $this->log("No processing errors found.");
        }

        if ($batchJob->downloadUrl !== null && $batchJob->downloadUrl->url !== null) {
            $xmlResponse = $this->adwordsBatchs->downloadResults($batchJob, $uploadUrl);
            $this->storeResults($batchJobId, $xmlResponse);
            $mutateResults = $this->adwordsBatchs->convertXMLToObjectCollection($xmlResponse);

            $this->updateState('download.success', ['status' => 'DONE']);

            $this->log((count($mutateResults)) . " operations returned. ".$mutateResults->getErrors()->count()." errors found.");

            return $mutateResults;
        } else {
            throw new BatchJobException("No results were fetched from batch job $batchJob->id .");
        }
    }


    /**
     * @param Exception $e
     * @param           $type
     *
     * @throws Exception
     */
    protected function throwUploadFailError(Exception $e)
    {
        $errors = collect(['Build error' => [[
            'message' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine()
        ]]]);
        $status = 'error';
        $this->updateState('upload.fail', compact("errors", 'status'));

        throw new Exception($e->getMessage(), 0, $e);
    }

    /**
     * @param Exception $e
     *
     * @throws Exception
     */
    protected function throwDownloadFailError(Exception $e)
    {
        $errors = collect(['Results download fail' => [[
            'message' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine()
        ]]]);
        $this->updateState('download.fail', compact("errors"));

        throw new Exception($e->getMessage(), 0, $e);
    }

    /**
     * @param $state
     * @param $data
     */
    protected function updateState($state, $data)
    {
        $this->fireEvent("idmkr.adwords.operations_director.$state", $data);
    }

    /**
     * @param $string
     */
    protected function log($string)
    {
        if(\App::runningInConsole()) {
            echo $string . "\n";
        }
    }

    /**
     * @param $batchJobId
     * @param $operations
     */
    protected function storeOperations($batchJobId, $operations)
    {
        /** @var Operation $operation */
        foreach($operations as $operation) {
            $operation->OperationType = class_basename($operation->operand);
        }
        $this->storeBatchJobFile($batchJobId, 'operations.json', json_encode($operations));
    }

    /**
     * @param $batchJobId
     * @param $xmlResponse
     */
    protected function storeResults($batchJobId, $xmlResponse)
    {
        $this->storeBatchJobFile($batchJobId, 'results.xml', $xmlResponse);
    }

    /**
     * @param $batchJobId
     * @param $file
     * @param $contents
     */
    private function storeBatchJobFile($batchJobId, $file, $contents)
    {
        Storage::put("adwords_batch_jobs/$batchJobId/$file", $contents, 'private');
    }
}