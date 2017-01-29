<?php namespace Idmkr\Adwords\Handlers\Budget;

use Budget;

interface BudgetDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  mixed  $data 
	 * @return Budget
	 */
	public function prepare($data) : Budget;

}
