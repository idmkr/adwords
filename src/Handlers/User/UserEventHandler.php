<?php namespace Idmkr\Adwords\Handlers\User;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\User;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class UserEventHandler extends BaseEventHandler implements UserEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.user.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.user.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.user.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.user.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.user.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.user.deleted', __CLASS__.'@deleted');
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
	public function created(User $user)
	{
		$this->flushCache($user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(User $user, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(User $user)
	{
		$this->flushCache($user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(User $user)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(User $user)
	{
		$this->flushCache($user);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\User  $user
	 * @return void
	 */
	protected function flushCache(User $user)
	{
		$this->app['cache']->forget('idmkr.adwords.user.all');

		$this->app['cache']->forget('idmkr.adwords.user.'.$user->id);
	}

}
