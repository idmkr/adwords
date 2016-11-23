<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Idmkr\Adwords\Models\User']
		);

		// Subscribe the registered event handler
		$this->app['events']->subscribe('idmkr.adwords.user.handler.event');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('idmkr.adwords.user', 'Idmkr\Adwords\Repositories\User\UserRepository');

		// Register the data handler
		$this->bindIf('idmkr.adwords.user.handler.data', 'Idmkr\Adwords\Handlers\User\UserDataHandler');

		// Register the event handler
		$this->bindIf('idmkr.adwords.user.handler.event', 'Idmkr\Adwords\Handlers\User\UserEventHandler');

		// Register the validator
		$this->bindIf('idmkr.adwords.user.validator', 'Idmkr\Adwords\Validator\User\UserValidator');
	}

}
