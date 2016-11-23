<?php namespace Idmkr\Adwords\Handlers\Ad;

use Idmkr\Adwords\Models\Ad;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface AdEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a ad is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a ad is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @return mixed
	 */
	public function created(Ad $ad);

	/**
	 * When a ad is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Ad $ad, array $data);

	/**
	 * When a ad is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @return mixed
	 */
	public function updated(Ad $ad);

	/**
	 * When a ad is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @return mixed
	 */
	public function deleting(Ad $ad);

	/**
	 * When a ad is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Ad  $ad
	 * @return mixed
	 */
	public function deleted(Ad $ad);

}
