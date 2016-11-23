<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class CampaignsServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.campaigns.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.campaigns', 'Idmkr\Adwords\Repositories\Campaigns\CampaignsRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.campaigns.handler.data', 'Idmkr\Adwords\Handlers\Campaigns\CampaignsDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.campaigns.handler.event', 'Idmkr\Adwords\Handlers\Campaigns\CampaignsEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.campaigns.validator', 'Idmkr\Adwords\Validator\Campaigns\CampaignsValidator');
	}

}
