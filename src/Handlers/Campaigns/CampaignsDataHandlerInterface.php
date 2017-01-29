<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use Campaign;

interface CampaignsDataHandlerInterface {
	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  mixed $data
	 * @return mixed
	 */
	public function prepare($data) : Campaign;
}
 