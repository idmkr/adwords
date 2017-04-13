<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class AdServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.ad.handler.event');
	}
  
	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.ad', 'Idmkr\Adwords\Repositories\Ad\AdRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.ad.handler.data', 'Idmkr\Adwords\Handlers\Ad\AdDataHandler');
		$this->bindIf('idmkr.adwords.adgroupad.handler.data', 'Idmkr\Adwords\Handlers\Ad\AdGroupAdDataHandler');
		$this->bindIf('idmkr.adwords.expandedtextad.handler.data', 'Idmkr\Adwords\Handlers\Ad\ExpandedTextAdDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.ad.handler.event', 'Idmkr\Adwords\Handlers\Ad\AdEventHandler');
	}

}
  