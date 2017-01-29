<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use Campaign;
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class CampaignEventHandler extends BaseEventHandler implements CampaignsEventHandlerInterface {

	/** 
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.campaign.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.campaign.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.campaign.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.campaign.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.campaign.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.campaign.deleted', __CLASS__.'@deleted');
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
	public function created(Campaign $campaign)
	{
		$this->flushCache($campaign);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Campaign $campaign, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Campaign $campaign)
	{
		$this->flushCache($campaign);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Campaign $campaign)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Campaign $campaign)
	{
		$this->flushCache($campaign);
	}

	/**
	 * Flush the cache.
	 *
	 * @return void
	 */
	protected function flushCache(Campaign $campaign)
	{
		$this->app['cache']->forget('idmkr.adwords.campaigns.all');

		$this->app['cache']->forget('idmkr.adwords.campaigns.'.$campaign->id);
	}

}
