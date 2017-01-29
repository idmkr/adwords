<?php namespace Idmkr\Adwords\Collections;

use AdGroup;
use BiddingStrategyConfiguration;
use CpcBid;
use Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandler;
use Money;

/**
 * Class AdGroupCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class AdGroupCollection extends AdwordsCollection
{
    protected $dataHandler = AdgroupDataHandler::class;
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
}