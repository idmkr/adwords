<?php namespace Idmkr\Adwords\Repositories\Batch;

use BatchJob;
use BatchJobException;
use BatchJobOperation;
use BatchJobProcessingError;
use BatchJobUtils;
use Cartalyst\Support\Traits;
use Guzzle\Batch\Batch;
use Idmkr\Adwords\Collections\MutateResultCollection;
use Idmkr\Adwords\Handlers\Batch\BatchDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Predicate;
use Selector;
use Symfony\Component\Finder\Finder;
use XmlDeserializer;

class BatchJobRepository extends AdwordsRepository implements BatchJobRepositoryInterface
{
    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        parent::__construct($app);

        $this->requireService("Util/".$this->getAdwordsApiVersion()."/BatchJobUtils", false);
    }

    /**
     * @param AdWordsUser $adWordsUser
     * @param null        $batchJobData
     *
     * @return BatchJob|null
     */
    public function create(AdWordsUser $adWordsUser, $batchJobData = null) : BatchJob
    {
        return $this->mutate($adWordsUser, [$batchJobData]);
    }

    /**
     * @param AdWordsUser $user
     * @param             $predicate
     * @return \BatchJob|null
     */
    public function find(AdWordsUser $user, $id) : BatchJob
    {
        return $this->get($user, ["Id","Status", 'DownloadUrl', 'ProcessingErrors', 'ProgressStats'], $id)[0] ?? null;
    }

    /**
     * @param AdWordsUser $user
     * @return BatchJob[]
     */
    public function findAll(AdWordsUser $user) : Array
    {
        return $this->get($user, ["Id","Status"], new \Predicate("Status", "EQUALS", "ACTIVE"));
    }

    /**
     * @param AdWordsUser $adwordsUser
     * @param array       $operations
     */
    public function uploadOperations(AdWordsUser $adwordsUser, array $operations)
    {
        $batchJob = $this->create($adwordsUser);
        $batchJobUtils = new BatchJobUtils($batchJob->uploadUrl->url);
        $batchJobUtils->UploadBatchJobOperations($operations);

        return $batchJob;
    }

    /**
     * @param AdWordsUser   $adwordsUser
     * @param               $batchJobId
     * @param callable|null $pollCallback
     *
     * @return BatchJob|null
     */
    public function poll(AdWordsUser $adwordsUser, $batchJobId, $sleepSeconds = 30, callable $pollCallback = null)
    {
        // Poll for completion of the batch job using an exponential back off.
        $pollAttempts = 0;
        $isPending = true;
        $maxPollAttempts = 60;

        do {
            echo "Sleeping $sleepSeconds seconds...";
            sleep($sleepSeconds);
            $batchJob = $this->find($adwordsUser, $batchJobId);
            $perc = $batchJob->progressStats->estimatedPercentExecuted;

            if(is_callable($pollCallback)) {
                $pollCallback($batchJob->status, $batchJob->progressStats->numOperationsExecuted, $perc);
            }

            if ($batchJob->status !== 'ACTIVE' &&
                $batchJob->status !== 'AWAITING_FILE' &&
                $batchJob->status !== 'CANCELING'
            ) {
                $isPending = false;
            }


            $pollAttempts++;
            $sleepSeconds *=  pow(1.5, $pollAttempts);
        } while ($isPending && $pollAttempts <= $maxPollAttempts);

        if ($isPending) {
            throw new BatchJobException("Job is still pending state after polling $maxPollAttempts times.");
        }

        return $batchJob;
    }

    /**
     * @param BatchJob $batchJob
     *
     * @param string   $uploadUrl
     *
     * @return \MutateResult[]|null
     */
    public function downloadResults(BatchJob $batchJob, $uploadUrl)
    {
        if ($batchJob->downloadUrl !== null && $batchJob->downloadUrl->url !== null) {
            $batchJobUtils = new BatchJobUtils($uploadUrl);

            $xmlResponse = $batchJobUtils->DownloadBatchJobResults($batchJob->downloadUrl->url);
            printf("Downloaded results from %s:\n", $batchJob->downloadUrl->url);

            return $xmlResponse;
        } else {
            printf("No results available for download.\n");
            return null;
        }
    }

    /**
     * @param string $xmlResponse
     *
     * @return MutateResultCollection|null
     */
    public function convertXMLToObjectCollection($xmlResponse)
    {
        $deserializer = new XmlDeserializer(BatchJobUtils::$CLASS_MAP);
        $mutateResponse = $deserializer->ConvertXmlToObject($xmlResponse);

        dump($mutateResponse);

        return is_object($mutateResponse) && $mutateResponse->rval ? new MutateResultCollection($mutateResponse->rval) : null;
    }

    /**
     * @param array $errorList
     *
     * @return Collection
     */
    public function groupErrors(Array $errorList)
    {
        return (new Collection($errorList))
            ->map(function ($error) {
                return [
                    "errorString" => $error->errorString,
                    "trigger" => $error->trigger,
                    "fieldPath" => $error->fieldPath
                ];
            })->groupBy('errorString')->map(function (Collection $errorsByType) {
                $errorsByType = $errorsByType->map(function ($error) {
                    return collect($error)->forget('errorString');
                });

                $max = 50;
                if($errorsByType->count() > $max) {
                    $errorsByType = $errorsByType->slice(0, $max)->push([
                        "..." => ($errorsByType->count() - $max)." others errors of the same type were returned."
                    ]);
                }

                return $errorsByType;
            })->toArray();
    }

    /**
     * Extract relevant results indexed by operation number
     *
     * @param $mutateResults
     *
     * @return array
     */
    public function extractResults($mutateResults) : Array
    {
        $results = [];
        (new Collection($mutateResults))->each(function (\MutateResult $mutateResult) use ($results) {
            if ($mutateResult->result) {
                $results[$mutateResult->index] = (new Collection($mutateResult->result))->first(function ($entityName, $entity) {
                    return $entity;
                });
            }
        });
        return $results;
    }

    /**
     * @param $mutateResults
     *
     * @return mixed
     */
    public function extractErrors($mutateResults) : Array
    {
        return (new Collection($mutateResults))->map(function ($mutateResult) {
            if ($mutateResult->errorList) {
                return $mutateResult->errorList->errors;
            }
            return false;
        })->flatten()->filter()->toArray();
    }

    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.batch';
    }

    protected function getDataHandler() : BatchDataHandlerInterface
    {
        return app('idmkr.adwords.batch.handler.data');
    }

    protected function getEntityClassName() : string
    {
        return 'BatchJob';
    }
}
