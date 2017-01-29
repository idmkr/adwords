<?php namespace Idmkr\Adwords\Handlers\Adgroup;

use AdGroup;
use BiddingStrategyConfiguration;
use CpcBid;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;
use Money;
use TempIdGenerator;

class AdgroupDataHandler extends DataHandler 
{
    use RequireAdWordsServiceTrait;

	public function __construct()
	{
		$this->requireService('AdGroupService');
	}

	public function prepareArray(array $data) : AdGroup
	{
		$adGroup = new AdGroup();
		$adGroup->name = $data["name"];

		if(!isset($data["id"])) {
			$this->requireService("Util/TempIdGenerator", false);
			$adGroup->id = TempIdGenerator::Generate();
		}

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
