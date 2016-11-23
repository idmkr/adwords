<?php namespace Idmkr\Adwords\Handlers\User;

use Idmkr\Adwords\Models\User;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface UserEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a user is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a user is created.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @return mixed
	 */
	public function created(User $user);

	/**
	 * When a user is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(User $user, array $data);

	/**
	 * When a user is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @return mixed
	 */
	public function updated(User $user);

	/**
	 * When a user is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @return mixed
	 */
	public function deleting(User $user);

	/**
	 * When a user is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @return mixed
	 */
	public function deleted(User $user);

}
