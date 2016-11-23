<?php namespace Idmkr\Adwords\Handlers\Campaigns;

interface CampaignsDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function prepare(array $data);

}
