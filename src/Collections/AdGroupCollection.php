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
}