<?php

use Carbon\Carbon;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\CampaignCollection;
use Idmkr\Adwords\Directors\AdGroupBatchOperationsDirector;
use Idmkr\Adwords\Operations\Builders\Ad\CustomizedAdBuilder;
use Idmkr\Adwords\Operations\Builders\AdGroup\AdGroupBuilder;
use Idmkr\Adwords\Operations\Builders\FeedItem\FeedItemBuilder;
use Idmkr\Adwords\Operations\Builders\Keyword\KeywordBuilder;
use LaravelGoogleAds\AdWords\AdWordsUser;

class BatchUploadTestCase extends TestCase
{
    /**
     * This test operate a complete batch upload in a specific campaign,
     * based on a json file.
     * Make sure your API Authentication is setup, replace the campaign id
     * with a test campaign id of yours and you're good to go
     *
     * @return void
     */
    public function testFeedBasedCampaignBatchUpload()
    {
        $campaignName = 'TestCampaign';
        $feedName = 'TestFeed';
        $adGroupTemplate = [
            'name' => 'Test {id}',
            'bid' => 10
        ];
        $adTemplates = [
            [
                'title1' => 'Need a new {ProductName} ?',
                'title2' => 'Only {Quantity} left',
                'description' => 'Prices starting from ${MinPrice}',
                'path1' => 'better',
                'path2' => 'ad'
            ],
            [
                'title1' => 'Buy {ProductName} now',
                'title2' => 'Only {Quantity} left',
                'description' => 'Prices starting from ${MinPrice}',
                'path1' => 'backup',
                'path2' => 'ad'
            ],
        ];
        $keywordTemplates = [
            '{Brand} {ProductName}',
            '+buy {ProductName}'
        ];

        $adwordsUser = new AdWordsUser();

        /**
         * @var \Idmkr\Adwords\Repositories\Feed\FeedRepository
         */
        $adwordsFeeds = app('idmkr.adwords.feed');

        /**
         * @var \Idmkr\Adwords\Repositories\Campaigns\CampaignsRepository
         */
        $adwordsCampaigns = app('idmkr.adwords.campaigns');

        $data = json_decode(file_get_contents('feed.json'));

        /*********
         * /* Campaign
         */
        $adwordsCampaign = $adwordsCampaigns->create(new Campaign(null, $campaignName));

        /*********
         * /* Feed
         */
        if (!$adwordsFeed = $adwordsFeeds->findByName($adwordsUser, $feedName)) {
            $adwordsFeed = $adwordsFeeds->create(
                $adwordsUser,
                $feedName,
                array_keys($data[0])
            );
        }


        $operationsDirector = new AdGroupBatchOperationsDirector($adwordsUser, $adGroupTemplate, $data);

        $adGroupsOperations = $operationsDirector
            ->setScope(new CampaignCollection([$adwordsCampaign]))
            ->build(new AdGroupBuilder())
            ->get();

        $operationsDirector
            ->setScope(new AdGroupCollection($adGroupsOperations))
            ->build(new FeedItemBuilder($adwordsFeed))
            ->build(new CustomizedAdBuilder($adTemplates, $feedName))
            ->build(new KeywordBuilder($keywordTemplates))

            ->upload()

            ->onFail(function (Exception $e, $reason) {
                printf([
                    "errors" => [$reason => [[
                        'message' => $e->getMessage(),
                        'file' => $e->getFile() . ':' . $e->getLine()
                    ]]],
                    "status" => "ERROR",
                    "completion_percentage" => 100,
                    "ended_at" => Carbon::now(),
                ]);
                throw new Exception($reason, 0, $e);
            })
            ->onUpload(function ($operations, $status, BatchJob $batchJob=null) {
                // Nothing to upload
                if(!count($operations)) {
                    printf("Nothing to upload. Aborting.");
                    printf([
                        'status' => $status,
                        'completion_percentage' => 100,
                        "ended_at" => Carbon::now(),
                    ]);
                }
                else {
                    printf([
                        'uploadUrl' => $batchJob->uploadUrl->url,
                        'adwords_batch_job_id' => $batchJob->id,
                        'status' => 'STARTING',
                    ]);
                }
            })
            ->onPolling(function ($operations, $status, $estimatedPercentExecuted) {
                printf([
                    "status" => $status,
                    "completion_percentage" => $estimatedPercentExecuted,
                    "operations_count" => count($operations)
                ]);
            })
            ->onDownload(function ($status, $errors) {
                printf([
                    "status" => $status,
                    "errors" => $errors
                ]);

                $this->assertEmpty($errors);
            });
    }
}