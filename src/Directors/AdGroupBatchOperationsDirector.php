<?php namespace Idmkr\Adwords\Directors;

use BatchJobException;
use Carbon\Carbon;
use Exception;
use Idmkr\Adwords\Collections\AdwordsCollection;
use Idmkr\Adwords\Operations\Builders\Builder;
use Idmkr\Adwords\Repositories\Batch\BatchRepository;
use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;

/**
 * Batch Operations Director
 *
 */
class AdGroupBatchOperationsDirector implements DirectorInterface
{
    /**
     * @var AdWordsUser
     */
    public $adwordsUser;
    /**
     * @var array
     */
    public $data;
    /**
     * @var array
     */
    public $adGroupTemplate;
    /**
     * @var Collection
     */
    public $scopeCollection;

    /**
     * @var \Operation[] $operations
     */
    private $operations = [];
    /**
     * @var callable|null
     */
    private $onFailCallback;
    /**
     * @var callable|null
     */
    private $onUploadCallback;
    /**
     * @var callable|null
     */
    private $onPollCallback;
    /**
     * @var callable|null
     */
    private $onDownloadCallback;
    /** @var  BatchRepository */
    private $adwordsBatchs;

    public function __construct(AdWordsUser $adwordsUser, array $adGroupTemplate, Array $data)
    {
        $this->adwordsUser = $adwordsUser;
        $this->adGroupTemplate = $adGroupTemplate;
        $this->data = $data;
        $this->adwordsBatchs = app('idmkr.adwords.batch');
    }

    /**
     * Build stuff
     *
     * @param Builder $builder
     *
     * @return static
     */
    public function build(Builder $builder)
    {
        $builder->setDirector($this);

        return $this->addOperations($builder->build($this->scopeCollection));
    }

    /**
     * @return \Operation[]
     */
    public function get($operator = null)
    {
        return $operator ? $this->filterOperations($operator) : $this->operations;
    }

    /**
     * @param $operations
     *
     * @return $this
     */
    public function addOperations($operations)
    {
        $this->operations = array_merge( $this->operations, $operations );
        return $this;
    }

    /**
     * @param AdwordsCollection $scopeCollection
     *
     * @return $this
     */
    public function setScope(AdwordsCollection $scopeCollection)
    {
        $this->scopeCollection = $scopeCollection;
        return $this;
    }

    private function filterOperations($operator)
    {
        $filteredOperations = [];
        foreach($this->operations as $operation) {
            if($operation->operator == $operator) {
                $filteredOperations[] = $operation;
            }
        }
        return $filteredOperations;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAdGroupTemplate()
    {
        return $this->adGroupTemplate;
    }

    public function upload()
    {
        try {
            $operations = $this->get();

            // Nothing to upload
            if(!count($operations)) {
                printf("Nothing to upload. Aborting.");
                call_user_func_array([$this, 'onUploadCallback'], [$operations, 'UPTODATE']);
            }

            $batchJob = $this->adwordsBatchs->uploadOperations($this->adwordsUser, $operations);
            call_user_func_array([$this, 'onUploadCallback'], [$operations, 'STARTING', $batchJob]);
   
            printf("Uploaded %d operations for batch job with ID %d.\n",
                count($operations), $batchJob->id);
        } catch (\SoapFault $e) {
            call_user_func_array([$this, 'onFailCallback'], [$e, 'AdWords Request Error']);
        } catch (Exception $e) {
            call_user_func_array([$this, 'onFailCallback'], [$e, 'Building Error']);
        }

        // Here $batchJob IS defined otherwise code would have thrown and stopped
        try {
            $this->awaitResults($batchJob->uploadUrl->url, $batchJob->id);
        } catch (Exception $e) {
            call_user_func_array([$this, 'onFailCallback'], [$e, 'AdWords Batch Generation Error']);
        }

        return $this;
    }

    private function awaitResults($uploadUrl, $batchJobId)
    {
        // Poll for completion of the batch job using an exponential back off.
        $pollAttempts = 0;
        $isPending = true;
        $pollFrequencySeconds = 30;
        $maxPollAttempts = 60;
        $errors = [];

        do {
            $sleepSeconds = $pollFrequencySeconds * pow(2, $pollAttempts);
            printf("Sleeping %d seconds...\n", $sleepSeconds);
            sleep($sleepSeconds);
            \DB::reconnect();
            $batchJob = $this->adwordsBatchs->find($this->adwordsUser, $batchJobId);
            $perc = $batchJob->progressStats->estimatedPercentExecuted;

            call_user_func_array([$this, 'onPollingCallback'], [
                $batchJob->progressStats->numOperationsExecuted,
                $batchJob->status == "DONE" ? "VALIDATING" :
                    ($batchJob->status == "AWAITING_FILE" ? "BUILDING" :
                        $batchJob->status),
                $perc == 100 ? 99 : $perc,
            ]);

            if ($batchJob->status !== 'ACTIVE' &&
                $batchJob->status !== 'AWAITING_FILE' &&
                $batchJob->status !== 'CANCELING'
            ) {
                $isPending = false;
            }
            $pollAttempts++;
        } while ($isPending && $pollAttempts <= $maxPollAttempts);

        if ($isPending) {
            throw new BatchJobException(
                sprintf("Job is still pending state after polling %d times.",
                    $maxPollAttempts));
        }

        if ($batchJob->processingErrors !== null) {
            $processingErrors = $this->adwordsBatchs->groupErrors($batchJob->processingErrors);

            $errors = array_merge($errors, $processingErrors);
        } else {
            printf("No processing errors found.\n");
        }

        if ($batchJob->downloadUrl !== null && $batchJob->downloadUrl->url !== null) {
            $mutateResults = $this->adwordsBatchs->downloadResults($batchJob, $uploadUrl);
        }
        $status = "UNKNOWN";
        if (isset($mutateResults) && $mutateResults) {
            $errorList = collect($mutateResults)->map(function ($mutateResult) {
                if ($mutateResult->errorList) {
                    return $mutateResult->errorList->errors;
                }
                return false;
            })->flatten()->filter()->toArray();

            if (!empty($errorList)) {
                printf(count($errorList) . " failed operations.\n");
                $mutateErrors = $this->adwordsBatchs->groupErrors($errorList);
                $errors = array_merge($errors, $mutateErrors);
                $status = "ERROR";
            } else {
                $status = "DONE";
            }
            printf((count($mutateResults) - count($errorList)) . " succeeded operations.\n");
        }

        call_user_func_array([$this, 'onDownloadCallback'], [
            "status" => $status,
            "ended_at" => Carbon::now(),
            "errors" => $errors,
            "completion_percentage" => 100
        ]);

        printf("Batch job ended.\n\n\n");
    }

    public function onFail(callable $failCallback)
    {
        $this->onFailCallback = $failCallback;
        return $this;
    }

    public function onUpload(callable $successCallback)
    {
        $this->onUploadCallback = $successCallback;
        return $this;
    }

    public function onPolling(callable $pollingCallback)
    {
        $this->onPollingCallback = $pollingCallback;
        return $this;
    }

    public function onDownload(callable $successCallback)
    {
        $this->onDownloadCallback = $successCallback;
        return $this;
    }
}