<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class BatchServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.batch.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.batch', 'Idmkr\Adwords\Repositories\Batch\BatchRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.batch.handler.data', 'Idmkr\Adwords\Handlers\Batch\BatchDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.batch.handler.event', 'Idmkr\Adwords\Handlers\Batch\BatchEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.batch.validator', 'Idmkr\Adwords\Validator\Batch\BatchValidator');
	}

}
 