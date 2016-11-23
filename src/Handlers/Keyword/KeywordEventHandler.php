<?php namespace Idmkr\Adwords\Handlers\Keyword;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Keyword;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class KeywordEventHandler extends BaseEventHandler implements KeywordEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.keyword.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.keyword.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.keyword.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.keyword.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.keyword.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.keyword.deleted', __CLASS__.'@deleted');
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
	public function created(Keyword $keyword)
	{
		$this->flushCache($keyword);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Keyword $keyword, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Keyword $keyword)
	{
		$this->flushCache($keyword);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Keyword $keyword)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Keyword $keyword)
	{
		$this->flushCache($keyword);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @return void
	 */
	protected function flushCache(Keyword $keyword)
	{
		$this->app['cache']->forget('idmkr.adwords.keyword.all');

		$this->app['cache']->forget('idmkr.adwords.keyword.'.$keyword->id);
	}

}
