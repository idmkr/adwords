<?php namespace Idmkr\Adwords\Repositories\Batch;

use BatchJob;
use BatchJobException;
use BatchJobOperation;
use BatchJobProcessingError;
use BatchJobUtils;
use Cartalyst\Support\Traits;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Predicate;
use Selector;
use Symfony\Component\Finder\Finder;
use XmlDeserializer;

class BatchRepository extends AdwordsRepository implements BatchRepositoryInterface
{
    use Traits\ContainerTrait, Traits\EventTrait, Traits\ValidatorTrait;

    /**
     * The Data handler.
     *
     * @var \Idmkr\Adwords\Handlers\Batch\BatchDataHandlerInterface
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        parent::__construct();

        $this->setContainer($app);

        $this->setDispatcher($app['events']);

        $this->data = $app['idmkr.adwords.batch.handler.data'];

        $this->setValidator($app['idmkr.adwords.batch.validator']);

        $this->requireService("Util/$this->version/BatchJobUtils", false);
    }

    /**
     * @param AdWordsUser $user
     * @param             $id
     * @return \BatchJob|null
     */
    public function find(AdWordsUser $user, $id)
    {
        // Get the BatchJobService, which loads the required classes.
        /** @var \BatchJobService $batchJobService */
        $batchJobService = $this->getService($user);

        $selector = new \Selector(
            ["Id","Status", 'DownloadUrl', 'ProcessingErrors', 'ProgressStats'],
            new \Predicate("Id", "EQUALS", $id)
        );

        /** @var \BatchJobPage $batchJobPage */
        $batchJobPage = $batchJobService->get($selector);

        if(!$batchJobPage->totalNumEntries)
            return null;

        foreach($batchJobPage->entries as $batchJob) {
                return $batchJob;
        }
        return null;
    }

    /**
     * @param AdWordsUser $user
     * @return \AdCustomizerFeedPage|null
     */
    public function findAll(AdWordsUser $user)
    {
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var \BatchJobService $batchJobService */
        $batchJobService = $this->getService($user);

        $selector = new \Selector(
            ["Id","Status"],
            new \Predicate("Status", "EQUALS", "ACTIVE")
        );

        /** @var \BatchJobPage $batchJobs */
        $batchJobs = $batchJobService->get($selector);

        if(!$batchJobs->totalNumEntries)
            return null;

        return $batchJobs->entries;
    }

    /**
     * @return \BatchJob
     */
    public function create(AdWordsUser $user)
    {
        // Get the service, which loads the required classes.
        $batchJobService = $this->getService($user);

        // Fire the 'idmkr.adwords.batch.creating' event
        if ($this->fireEvent('idmkr.adwords.batch.creating', [$user]) === false) {
            return false;
        }

        // Create a BatchJob.
        $addOp = new BatchJobOperation();
        $addOp->operator = 'ADD';
        $addOp->operand = new BatchJob();
        $addOps[] = $addOp;

        $result = $batchJobService->mutate($addOps);
        $batchJob = $result->value[0];

        // Check if the validation returned any errors
        if (!empty($batchJob->uploadUrl->url)) {
            // Fire the 'idmkr.adwords.batch.created' event
            $this->fireEvent('idmkr.adwords.batch.created', [$batchJob]);
        }

        return $batchJob;
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
            $deserializer = new XmlDeserializer(BatchJobUtils::$CLASS_MAP);
            $mutateResponse = $deserializer->ConvertXmlToObject($xmlResponse);
            if (empty($mutateResponse)) {
                printf("No results available.\n");
                return null;
            }

            return $mutateResponse->rval;
        } else {
            printf("No results available for download.\n");
            return null;
        }
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

    public function getService(AdWordsUser $user)
    {
        return $user->GetService('BatchJobService', $this->version);
    }
}
