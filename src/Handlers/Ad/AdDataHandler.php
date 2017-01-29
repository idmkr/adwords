<?php namespace Idmkr\Adwords\Handlers\Ad;

use Ad;
use Idmkr\Adwords\Handlers\DataHandler;

class AdDataHandler extends DataHandler
{
	/**
	 * build an Ad
	 *
	 * @param array $data the attributes
	 */
	public function prepareArrayItem(array $data)
	{
		$ad = new Ad();

		$ad->headlinePart1 = $data["title1"];
		$ad->headlinePart2 = $data["title2"];
		$ad->description = $data["description"];
		$ad->finalUrls = [$data["url"]];

		return $ad;
	}
}
