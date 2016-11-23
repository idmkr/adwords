<?php namespace Idmkr\Adwords\Handlers\Generation;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Generation;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class GenerationEventHandler extends BaseEventHandler implements GenerationEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.generation.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.generation.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.generation.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.generation.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.generation.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.generation.deleted', __CLASS__.'@deleted');
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
	public function created(Generation $generation)
	{
		$this->flushCache($generation);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Generation $generation, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Generation $generation)
	{
		$this->flushCache($generation);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Generation $generation)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Generation $generation)
	{
		$this->flushCache($generation);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @return void
	 */
	protected function flushCache(Generation $generation)
	{
		$this->app['cache']->forget('idmkr.adwords.generation.all');

		$this->app['cache']->forget('idmkr.adwords.generation.'.$generation->id);
	}

}
