<?php namespace Idmkr\Adwords\Providers;

use Cartalyst\Support\ServiceProvider;

class BudgetServiceProvider extends ServiceProvider {

    /**
     * {@inheritDoc}
     */
    public function boot()
    {

        // Subscribe the registered event handler
        $this->app['events']->subscribe('idmkr.adwords.budget.handler.event');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register the repository
        $this->bindIf('idmkr.adwords.budget', 'Idmkr\Adwords\Repositories\Budget\BudgetRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.budget.handler.data', 'Idmkr\Adwords\Handlers\Budget\BudgetDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.budget.handler.event', 'Idmkr\Adwords\Handlers\Budget\BudgetEventHandler');

    }

}
