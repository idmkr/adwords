<?php namespace Idmkr\Adwords\Collections;

use AdGroup;
use BiddingStrategyConfiguration;
use CpcBid;
use Money;

/**
 * Class AdGroupCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class AdGroupCollection extends AdwordsCollection
{
    /**
     * @var AdGroup[]
     */
    public $items;

    public function getPropertiesMap()
    {
        return [
            'bid' => function (AdGroup $adGroup) {
                return $adGroup->biddingStrategyConfiguration->bids[0]->bid->microAmount;
            },
            'enabled' => function (AdGroup $adGroup) {
                return $adGroup->status == 'ENABLED';
            }
        ];
    }

    public function parseArrayItem(array $data) : AdGroup
    {
        $adGroup = new AdGroup();
        
        $adGroup->name = $data["name"];

        $biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $bid = new CpcBid();
        $bid->bid = new Money(intval($data["bid"]*$this->microAmountFactor));
        $biddingStrategyConfiguration->bids[] = $bid;

        $adGroup->biddingStrategyConfiguration = $biddingStrategyConfiguration;

        if(isset($data["enabled"])) {
            $adGroup->status = $data["enabled"] ? 'ENABLED' : 'PAUSED';
        }
        
        return $adGroup;
    }
}