<?php namespace Idmkr\Adwords\Handlers\Budget;

use Budget;
use Idmkr\Adwords\Handlers\DataHandler;
use Money;

class BudgetDataHandler extends DataHandler
{
	public function prepareArray(array $data) : Budget
	{
		// Create the shared budget (required).
		$budget = new Budget();
		$budget->name = $data["name"];
		$budget->amount = new Money($data["amount"]*$this->microAmountFactor);
		$budget->deliveryMethod = 'STANDARD';

		return $budget;
	}

	public function prepareInt(int $id) : Budget
    {
        $budget = new Budget();
        $budget->budgetId = $id;

        return $budget;
    }
}
