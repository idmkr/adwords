<?php namespace Idmkr\Adwords\Repositories\Adgroup;

use LaravelGoogleAds\AdWords\AdWordsUser;

interface AdgroupRepositoryInterface {
	/**
	 * Creates a adwords adgroup with the given data.
	 *
	 * @param  AdWordsUser  $user
	 * @param  array  $data
	 * @return int the id
	 */
	public function create(AdWordsUser $user, $data);
}
