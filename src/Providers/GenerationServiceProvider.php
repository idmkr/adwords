<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class GenerationServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Idmkr\Adwords\Models\Generation']
		);

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.generation.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.generation', 'Idmkr\Adwords\Repositories\Generation\GenerationRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.generation.handler.data', 'Idmkr\Adwords\Handlers\Generation\GenerationDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.generation.handler.event', 'Idmkr\Adwords\Handlers\Generation\GenerationEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.generation.validator', 'Idmkr\Adwords\Validator\Generation\GenerationValidator');
	}

}
