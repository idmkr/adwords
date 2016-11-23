<?php namespace Idmkr\Adwords\Handlers\Ad;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Ad;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class AdEventHandler extends BaseEventHandler implements AdEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.ad.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.ad.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.ad.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.ad.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.ad.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.ad.deleted', __CLASS__.'@deleted');
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
	public function created(Ad $ad)
	{
		$this->flushCache($ad);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Ad $ad, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Ad $ad)
	{
		$this->flushCache($ad);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Ad $ad)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Ad $ad)
	{
		$this->flushCache($ad);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @return void
	 */
	protected function flushCache(Ad $ad)
	{
		$this->app['cache']->forget('idmkr.adwords.ad.all');

		$this->app['cache']->forget('idmkr.adwords.ad.'.$ad->id);
	}

}
