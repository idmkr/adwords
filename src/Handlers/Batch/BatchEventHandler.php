<?php namespace Idmkr\Adwords\Handlers\Batch;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Batch;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;
use LaravelGoogleAds\AdWords\AdWordsUser;

class BatchEventHandler extends BaseEventHandler implements BatchEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.batch.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.batch.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.batch.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.batch.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.batch.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.batch.deleted', __CLASS__.'@deleted');
	}

	/**
	 * Creating a batch job for this specific adwords user
	 *
	 * {@inheritDoc}
	 */
	public function creating($adWordsUser)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function created(\BatchJob $job)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(\BatchJob $job, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(\BatchJob $job)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(\BatchJob $job)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(\BatchJob $job)
	{

	}

}
