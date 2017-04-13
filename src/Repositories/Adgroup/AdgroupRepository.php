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
use Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandler;
use Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Money;
use TargetingSetting;
use TargetingSettingDetail;

class AdgroupRepository extends AdwordsRepository
{
    public function __construct(Container $app)
    {
        parent::__construct($app);

        $this->requireService("AdGroupService");
        $this->requireService("AdGroupAdService");
    }

    /**
     * @param Campaign $campaign
     * @param $adGroup
     * @param $operator
     *
     * @return AdGroupOperation
     */
    public function     buildCampaignOperation(\Campaign $campaign, $adGroup, $operator = 'ADD')
    {
        $adGroup = $this->getDataHandler()->prepare($adGroup);
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

        return $operation;
    }

    /**
     * @param AdWordsUser $adwordsUser
     * @param int         $campaign_id
     *
     * @return AdGroupCollection
     */
    public function findByCampaignId(AdWordsUser $adwordsUser, $campaign_id) : AdGroupCollection
    {
        return new AdGroupCollection(
            $this->get($adwordsUser,
                ["Id","CampaignId","Name", "CpcBid", "Status"], [
                    new \Predicate("CampaignId", "EQUALS", $campaign_id),
                    new \Predicate("Status", "IN", ['ENABLED', 'PAUSED']),
                ],
                new \OrderBy('Id')
            )
        ); 
    }

    /**
     * @return string
     */
    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.adgroup';
    }

    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return 'AdGroup';
    }

    protected function getDataHandler() : AdgroupDataHandler
    {
        return app('idmkr.adwords.adgroup.handler.data');
    }
}


