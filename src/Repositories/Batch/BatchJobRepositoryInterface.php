<?php namespace Idmkr\Adwords\Repositories\Batch;

use BatchJob;
use LaravelGoogleAds\AdWords\AdWordsUser;

interface BatchJobRepositoryInterface
{
    public function create(AdWordsUser $adWordsUser, $entity = []) : BatchJob;
    public function find(AdWordsUser $user, $id) : BatchJob;
    public function findAll(AdWordsUser $user) : Array;
    public function uploadOperations(AdWordsUser $adwordsUser, array $operations);
    public function poll(AdWordsUser $adwordsUser, $batchJobId, $sleepSeconds = 30, callable $pollCallback = null);
    public function downloadResults(BatchJob $batchJob, $uploadUrl);
}
