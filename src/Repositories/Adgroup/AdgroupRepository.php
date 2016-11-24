<?php namespace Idmkr\Adwords\Repositories\Adgroup;

use AdGroup;
use AdGroupOperation;
use AdGroupServiceError;
use ApiError;
use BiddingStrategyConfiguration;
use Campaign;
use Cartalyst\Support\Traits;
use CpcBid;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\CampaignCollection;
use Idmkr\Adwords\Iterators\CampaignIterator;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;
use League\Fractal\Scope;
use Money;
use Symfony\Component\Finder\Finder;
use TargetingSetting;
use TargetingSettingDetail;
use TempIdGenerator;

class AdgroupRepository extends AdwordsRepository implements AdgroupRepositoryInterface
{
    /**
     * Runs the example.
     *
     * @param AdWordsUser $user the user to run the example with
     * @param Collection  $adGroups the data to insert
     */
    function create(AdWordsUser $user, $adGroups)
    {
        // Get the service, which loads the required classes.
        /** @var \AdGroupService $adGroupService */
        $adGroupService = $user->GetService('AdGroupService', $this->version);

        $adGroupsChunks = $adGroups->chunk(ceil($adGroups->count() / 20));

        $ids = $adGroupsChunks->first()->map(function($adGroupData) use($adGroupService) {
            $operations = [];//$adGroupsChunk->map(function ($adGroupData) {
                // Create ad group.
                $adGroup = new AdGroup();
                $adGroup->campaignId = $adGroupData["campaignId"];
                $adGroup->name = $adGroupData["name"];
                // Set bids (required).
                $bid = new CpcBid();
                $bid->bid = new Money($adGroupData["bid"]*1000000);
                $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
                $biddingStrategyConfiguration->bids[] = $bid;
                $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;
                // Set additional settings (optional).
                $adGroup->status = 'ENABLED';
                // Targeting restriction settings. Depending on the criterionTypeGroup
                // value, most TargetingSettingDetail only affect Display campaigns.
                // However, the USER_INTEREST_AND_LIST value works for RLSA campaigns -
                // Search campaigns targeting using a remarketing list.
                $targetingSetting = new TargetingSetting();
                // Restricting to serve ads that match your ad group placements.
                // This is equivalent to choosing "Target and bid" in the UI.
                $targetingSetting->details[] =
                    new TargetingSettingDetail('PLACEMENT', false);
                // Using your ad group verticals only for bidding. This is equivalent
                // to choosing "Bid only" in the UI.
                $targetingSetting->details[] =
                    new TargetingSettingDetail('VERTICAL', true);
                $adGroup->settings[] = $targetingSetting;
                // Create operation.
                $operation = new AdGroupOperation();
                $operation->operand = $adGroup;
                $operation->operator = 'ADD';
                $operations[] = $operation;
            //});
            // Make the mutate request.
            $result = $adGroupService->mutate($operations);
            // Display result.
            $adGroups = $result->value;
            $ids = [];
            foreach ($adGroups as $adGroup) {
                $ids[] = $adGroup->id;
            }
            return $ids;
        });

        return $ids;
    }

    /**
     * Builds objects of AdGroupOperation for creating ad groups for campaigns in
     * the specified campaigns.
     *
     * @param CampaignCollection $campaigns the id of the campaign to insert into
     * @param AdGroupCollection  $adGroupsData  an array of data
     * @param string $operator
     * 
*@return array an array of AdGroupOperation
     */
    public function buildCampaignsOperations(
        CampaignCollection $campaigns, 
        AdGroupCollection $adGroups, 
        $operator = 'ADD'
    ) {
        $operations = array();

        foreach($campaigns as $campaign) {
            $operations = array_merge($operations,
                $this->buildCampaignOperations($campaign, $adGroups, $operator)
            );
        }

        return $operations;
    }


    /**
     * @param Campaign $campaign
     * @param AdGroupCollection $adGroups
     * @param $operator
     *
     * @return array
     */
    public function buildCampaignOperations(\Campaign $campaign, AdGroupCollection $adGroups, $operator = 'ADD')
    {
        $this->requireService("AdGroupService");
        $this->requireService("AdGroupAdService");
        $this->requireService("Util/TempIdGenerator", false);

        $operations = [];

        /** @var AdGroup $adGroup */
        foreach($adGroups as $adGroup) {
            $adGroup->campaignId = $campaign->id;

            $operation = new AdGroupOperation();
            if($operator == "REMOVE") {
                $operation->operator = 'SET';
                $adGroup->status = "REMOVED";
            }
            else {
                $operation->operator = $operator;
            }
            $operation->operand = $adGroup;

            $operations[] = $operation;
        }

        return $operations;
    }

    /**
     * @param AdWordsUser $adwordsUser
     * @param int         $campaign_id
     *
     * @return AdGroupCollection|null
     */
    public function findByCampaignId(AdWordsUser $adwordsUser, $campaign_id)
    {
        // Get the AdGroupService, which loads the required classes.
        /** @var \AdGroupService $adGroupService */
        $adGroupService = $adwordsUser->GetService('AdGroupService',
            $this->version);

        $selector = new \Selector(
            ["Id","Name", "CpcBid", "Status"],
            [
                new \Predicate("CampaignId", "EQUALS", $campaign_id),
                new \Predicate("Status", "IN", ['ENABLED', 'PAUSED']),
            ]
        );

        /** @var \AdGroupPage $adGroupsPage */
        $adGroupsPage = $adGroupService->get($selector);

        if(!$adGroupsPage->totalNumEntries)
            return null;

        return new AdGroupCollection($adGroupsPage->entries);
    }
}


