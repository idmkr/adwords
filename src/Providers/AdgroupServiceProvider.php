<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class AdgroupServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.adgroup.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.adgroup', 'Idmkr\Adwords\Repositories\Adgroup\AdgroupRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.adgroup.handler.data', 'Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.adgroup.handler.event', 'Idmkr\Adwords\Handlers\Adgroup\AdgroupEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.adgroup.validator', 'Idmkr\Adwords\Validator\Adgroup\AdgroupValidator');
	}

}
