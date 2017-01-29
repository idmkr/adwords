<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use Campaign;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface CampaignsEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a campaigns is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a campaigns is created.
	 *
	 * @param  Campaign  $campaigns
	 * @return mixed
	 */
	public function created(Campaign $campaigns);

	/**
	 * When a campaigns is being updated.
	 *
	 * @param  Campaign  $campaigns
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Campaign $campaigns, array $data);

	/**
	 * When a campaigns is updated.
	 *
	 * @param  Campaign  $campaigns
	 * @return mixed
	 */
	public function updated(Campaign $campaigns);

	/**
	 * When a campaigns is being deleted.
	 *
	 * @param  Campaign  $campaigns
	 * @return mixed
	 */
	public function deleting(Campaign $campaigns);

	/**
	 * When a campaigns is deleted.
	 *
	 * @param  Campaign  $campaigns
	 * @return mixed
	 */
	public function deleted(Campaign $campaigns);

}
