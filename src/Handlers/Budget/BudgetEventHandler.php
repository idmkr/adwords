<?php namespace Idmkr\Adwords\Handlers\Budget;

use Illuminate\Events\Dispatcher;
use Idmkr\Adwords\Models\Budget;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class BudgetEventHandler extends BaseEventHandler implements BudgetEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.budget.creating', __CLASS__.'@creating');
		$dispatcher->listen('idmkr.adwords.budget.created', __CLASS__.'@created');

		$dispatcher->listen('idmkr.adwords.budget.updating', __CLASS__.'@updating');
		$dispatcher->listen('idmkr.adwords.budget.updated', __CLASS__.'@updated');

		$dispatcher->listen('idmkr.adwords.budget.deleted', __CLASS__.'@deleting');
		$dispatcher->listen('idmkr.adwords.budget.deleted', __CLASS__.'@deleted');
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
	public function created(Budget $budget)
	{
		$this->flushCache($budget);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Budget $budget, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Budget $budget)
	{
		$this->flushCache($budget);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleting(Budget $budget)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Budget $budget)
	{
		$this->flushCache($budget);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @return void
	 */
	protected function flushCache(Budget $budget)
	{
		$this->app['cache']->forget('idmkr.adwords.budget.all');

		$this->app['cache']->forget('idmkr.adwords.budget.'.$budget->id);
	}

}
