<?php namespace Idmkr\Adwords\Repositories\Budget;

use Budget;
use LaravelGoogleAds\AdWords\AdWordsUser;

interface BudgetRepositoryInterface {
    /**
     * Prepares the given data for being stored.
     *
     * @param  mixed $data
     * @return Budget
     */
    public function create(AdWordsUser $adWordsUser, $data) : Budget;
}
