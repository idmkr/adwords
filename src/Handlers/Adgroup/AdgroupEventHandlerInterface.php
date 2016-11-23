<?php namespace Idmkr\Adwords\Handlers\Adgroup;

use Idmkr\Adwords\Models\Adgroup;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface AdgroupEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a adgroup is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a adgroup is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @return mixed
	 */
	public function created(Adgroup $adgroup);

	/**
	 * When a adgroup is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Adgroup $adgroup, array $data);

	/**
	 * When a adgroup is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @return mixed
	 */
	public function updated(Adgroup $adgroup);

	/**
	 * When a adgroup is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @return mixed
	 */
	public function deleting(Adgroup $adgroup);

	/**
	 * When a adgroup is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Adgroup  $adgroup
	 * @return mixed
	 */
	public function deleted(Adgroup $adgroup);

}
