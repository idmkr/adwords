<?php namespace Idmkr\Adwords\Handlers\Adgroup;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Adgroup;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class AdgroupEventHandler extends BaseEventHandler implements AdgroupEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.adgroup.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.adgroup.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.adgroup.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.adgroup.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.adgroup.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.adgroup.deleted', __CLASS__.'@deleted');
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
	public function created(Adgroup $adgroup)
	{
		$this->flushCache($adgroup);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Adgroup $adgroup, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Adgroup $adgroup)
	{
		$this->flushCache($adgroup);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Adgroup $adgroup)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Adgroup $adgroup)
	{
		$this->flushCache($adgroup);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @return void
	 */
	protected function flushCache(Adgroup $adgroup)
	{
		$this->app['cache']->forget('idmkr.adwords.adgroup.all');

		$this->app['cache']->forget('idmkr.adwords.adgroup.'.$adgroup->id);
	}

}
