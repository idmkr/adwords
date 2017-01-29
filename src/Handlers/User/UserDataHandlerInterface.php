<?php namespace Idmkr\Adwords\Handlers\User;

use LaravelGoogleAds\AdWords\AdWordsUser;

interface UserDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  $data
	 * @return mixed
	 */
	public function prepare($data) : AdWordsUser;

}
