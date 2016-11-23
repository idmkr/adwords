<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Campaigns;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class CampaignsEventHandler extends BaseEventHandler implements CampaignsEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.campaigns.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.campaigns.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.campaigns.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.campaigns.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.campaigns.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.campaigns.deleted', __CLASS__.'@deleted');
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
	public function created(Campaigns $campaigns)
	{
		$this->flushCache($campaigns);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Campaigns $campaigns, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Campaigns $campaigns)
	{
		$this->flushCache($campaigns);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Campaigns $campaigns)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Campaigns $campaigns)
	{
		$this->flushCache($campaigns);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @return void
	 */
	protected function flushCache(Campaigns $campaigns)
	{
		$this->app['cache']->forget('idmkr.adwords.campaigns.all');

		$this->app['cache']->forget('idmkr.adwords.campaigns.'.$campaigns->id);
	}

}
