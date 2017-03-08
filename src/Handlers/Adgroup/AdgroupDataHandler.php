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

	public function prepareString(string $id) : AdGroup
	{
		return $this->prepareInt((int)$id);

	}

	public function prepareInt(int $id) : AdGroup
	{
		$adGroup = new AdGroup;
		$adGroup->id = $id;

		return $adGroup;
	}

	public function prepareArray(array $data) : AdGroup
	{
        
		$adGroup = new AdGroup();
		$adGroup->name = $data["name"];

		if(!isset($data["id"])) {
			$this->requireService("Util/TempIdGenerator", false);
			$adGroup->id = TempIdGenerator::Generate();
		}
		else {
			$adGroup->id = $data["id"];
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
