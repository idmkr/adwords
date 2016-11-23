<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use Idmkr\Adwords\Models\Campaigns;
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
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @return mixed
	 */
	public function created(Campaigns $campaigns);

	/**
	 * When a campaigns is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Campaigns $campaigns, array $data);

	/**
	 * When a campaigns is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @return mixed
	 */
	public function updated(Campaigns $campaigns);

	/**
	 * When a campaigns is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @return mixed
	 */
	public function deleting(Campaigns $campaigns);

	/**
	 * When a campaigns is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Campaigns  $campaigns
	 * @return mixed
	 */
	public function deleted(Campaigns $campaigns);

}
