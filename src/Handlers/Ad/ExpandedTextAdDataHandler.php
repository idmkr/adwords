<?php namespace Idmkr\Adwords\Handlers\Ad;

use Ad;
use ExpandedTextAd;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;
use TempIdGenerator;

class ExpandedTextAdDataHandler extends DataHandler
{
	use RequireAdWordsServiceTrait;

	public function __construct()
	{
		$this->requireService('AdGroupService');
	}


	/**
	 * build an ExpandedTextAd
	 *
	 * @param array $data the attributes
	 */
	public function prepareArray(array $data) : ExpandedTextAd
	{
		$this->requireData($data, ['title1', 'title2', 'description', 'url']);
		$expandedTextAd = new ExpandedTextAd();

		$expandedTextAd->headlinePart1 = $data["title1"];
		$expandedTextAd->headlinePart2 = $data["title2"];
		$expandedTextAd->description = $data["description"];
		$expandedTextAd->finalUrls = [$data["url"]];

		if(!isset($data["id"])) {
			$this->requireService("Util/TempIdGenerator", false);
			$expandedTextAd->id = TempIdGenerator::Generate();
		}

		if(isset($data["path2"]) && $data["path2"] && (!isset($data["path1"]) || !$data["path1"])) {
			$data["path1"] = $data["path2"];
			$data["path2"] = null;
		}

		if(isset($data["path1"]) && $data["path1"]) {
			$expandedTextAd->path1 = $data["path1"];
		}
		if(isset($data["path2"]) && $data["path2"]) {
			$expandedTextAd->path2 = $data["path2"];
		}

		return $expandedTextAd;
	}

	/**
	 * build an ExpandedTextAd
	 *
	 * @param array $data the attributes
	 */
	public function prepareModel($data) : ExpandedTextAd
	{
		$expandedTextAd = new ExpandedTextAd();

		$expandedTextAd->id = $data["adwords_id"];
		$expandedTextAd->headlinePart1 = $data["headlinePart1"];
		$expandedTextAd->headlinePart2 = $data["headlinePart2"];
		$expandedTextAd->path1 = $data["path1"];
		$expandedTextAd->path2 = $data["path2"];
		$expandedTextAd->description = $data["description"];
		$expandedTextAd->finalUrls = $data["finalUrls"];

		return $expandedTextAd;
	}
}
