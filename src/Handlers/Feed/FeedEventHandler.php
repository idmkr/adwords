<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class FeedEventHandler extends BaseEventHandler implements FeedEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.feed.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.feed.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.feed.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.feed.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.feed.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.feed.deleted', __CLASS__.'@deleted');
	}

	/**
	 * {@inheritDoc}
	 */
	public function creating(array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function created(\AdCustomizerFeed $feed)
	{
		$this->flushCache($feed);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(\AdCustomizerFeed $feed, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(\AdCustomizerFeed $feed)
	{
		$this->flushCache($feed);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(\AdCustomizerFeed $feed)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(\AdCustomizerFeed $feed)
	{
		$this->flushCache($feed);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  AdCustomizerFeed  $feed
	 * @return void
	 */
	protected function flushCache(\AdCustomizerFeed $feed)
	{
		$this->app['cache']->forget('idmkr.adwords.feed.all');

		$this->app['cache']->forget('idmkr.adwords.feed.'.$feed->feedId);
	}

}
