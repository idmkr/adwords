<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class KeywordServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.keyword.handler.event');
	}
  
	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.keyword', 'Idmkr\Adwords\Repositories\Keyword\KeywordRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.keyword.handler.data', 'Idmkr\Adwords\Handlers\Keyword\KeywordDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.keyword.handler.event', 'Idmkr\Adwords\Handlers\Keyword\KeywordEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.keyword.validator', 'Idmkr\Adwords\Validator\Keyword\KeywordValidator');
	}

}
 