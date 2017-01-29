<?php namespace Idmkr\Adwords\Handlers\User;

use Idmkr\Adwords\Handlers\DataHandler;
use LaravelGoogleAds\AdWords\AdWordsUser;

class UserDataHandler extends DataHandler implements UserDataHandlerInterface
{
	public function prepare($data) : AdWordsUser
	{
		return new AdWordsUser();
	}

}
