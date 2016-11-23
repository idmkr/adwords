<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class FeedServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.feed.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.feed', 'Idmkr\Adwords\Repositories\Feed\FeedRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.feed.handler.data', 'Idmkr\Adwords\Handlers\Feed\FeedDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.feed.handler.event', 'Idmkr\Adwords\Handlers\Feed\FeedEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.feed.validator', 'Idmkr\Adwords\Validator\Feed\FeedValidator');
	}

}
 