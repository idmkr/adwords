<?php namespace Idmkr\Adwords\Handlers\Budget;

use Idmkr\Adwords\Models\Budget;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface BudgetEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a budget is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a budget is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @return mixed
	 */
	public function created(Budget $budget);

	/**
	 * When a budget is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Budget $budget, array $data);

	/**
	 * When a budget is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @return mixed
	 */
	public function updated(Budget $budget);

	/**
	 * When a budget is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @return mixed
	 */
	public function deleting(Budget $budget);

	/**
	 * When a budget is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Budget  $budget
	 * @return mixed
	 */
	public function deleted(Budget $budget);

}
