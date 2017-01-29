<?php namespace Idmkr\Adwords\Repositories\Budget;

use Budget;
use BudgetOperation;
use Idmkr\Adwords\Handlers\Budget\BudgetDataHandler;
use Idmkr\Adwords\Handlers\Budget\BudgetDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use LaravelGoogleAds\AdWords\AdWordsUser;

class BudgetRepository extends AdwordsRepository implements BudgetRepositoryInterface
{
    public function create(AdWordsUser $adWordsUser, $data) : Budget
    {
        return $this->mutate($adWordsUser, $this->getDataHandler()->prepare($data));
    }

    protected function getDataHandler() : BudgetDataHandler
    {
        return app('idmkr.adwords.budget.handler.data');
    }

    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.budget';
    }

    protected function getEntityClassName() : string
    {
        return 'Budget';
    }
}
