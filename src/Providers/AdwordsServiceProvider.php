<?php namespace Idmkr\Adwords\Providers;

use Illuminate\Support\ServiceProvider;

class AdwordsServiceProvider extends ServiceProvider {

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Subscribe the registered event handler
        $this->app['events']->subscribe('idmkr.adwords.adgroup.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.ad.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.batch.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.campaign.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.feed.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.generation.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.keyword.handler.event');
        $this->app['events']->subscribe('idmkr.adwords.user.handler.event');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register the repository
        $this->registerAdGroup();
        $this->registerAd();
        $this->registerBatch();
        $this->registerCampaigns();
        $this->registerFeed();
        $this->registerGeneration();
        $this->registerKeyword();
        $this->registerUser();
    }

    /**
     * {@inheritDoc}
     */
    public function registerAdGroup()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Adgroup\AdgroupRepositoryInterface',
            'Idmkr\Adwords\Repositories\Adgroup\AdgroupRepository'
        );
        $this->bindIf('idmkr.adwords.adgroup', 'Idmkr\Adwords\Repositories\Adgroup\AdgroupRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.adgroup.handler.data', 'Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.adgroup.handler.event', 'Idmkr\Adwords\Handlers\Adgroup\AdgroupEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.adgroup.validator', 'Idmkr\Adwords\Validator\Adgroup\AdgroupValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerAd()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Ad\AdRepositoryInterface',
            'Idmkr\Adwords\Repositories\Ad\AdRepository'
        );
        $this->bindIf('idmkr.adwords.ad', 'Idmkr\Adwords\Repositories\Ad\AdRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.ad.handler.data', 'Idmkr\Adwords\Handlers\Ad\AdDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.ad.handler.event', 'Idmkr\Adwords\Handlers\Ad\AdEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.ad.validator', 'Idmkr\Adwords\Validator\Ad\AdgroupValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerBatch()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Batch\BatchRepositoryInterface',
            'Idmkr\Adwords\Repositories\Batch\BatchRepository'
        );
        $this->bindIf('idmkr.adwords.batch', 'Idmkr\Adwords\Repositories\Batch\BatchRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.batch.handler.data', 'Idmkr\Adwords\Handlers\Batch\BatchDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.batch.handler.event', 'Idmkr\Adwords\Handlers\Batch\BatchEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.batch.validator', 'Idmkr\Adwords\Validator\Batch\BatchValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerCampaigns()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Campaigns\CampaignsRepositoryInterface',
            'Idmkr\Adwords\Repositories\Campaigns\CampaignsRepository'
        );
        $this->bindIf('idmkr.adwords.campaign', 'Idmkr\Adwords\Repositories\Campaigns\CampaignsRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.campaign.handler.data', 'Idmkr\Adwords\Handlers\Campaigns\CampaignsDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.campaign.handler.event', 'Idmkr\Adwords\Handlers\Campaigns\CampaignsEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.campaign.validator', 'Idmkr\Adwords\Validator\Campaigns\CampaignsValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerFeed()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Feed\FeedRepositoryInterface',
            'Idmkr\Adwords\Repositories\Feed\FeedRepository'
        );
        $this->bindIf('idmkr.adwords.feed', 'Idmkr\Adwords\Repositories\Feed\FeedRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.feed.handler.data', 'Idmkr\Adwords\Handlers\Feed\FeedDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.feed.handler.event', 'Idmkr\Adwords\Handlers\Feed\FeedEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.feed.validator', 'Idmkr\Adwords\Validator\Feed\FeedValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerGeneration()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Generation\GenerationRepositoryInterface',
            'Idmkr\Adwords\Repositories\Generation\GenerationRepository'
        );
        $this->bindIf('idmkr.adwords.adgroup', 'Idmkr\Adwords\Repositories\Generation\GenerationRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.adgroup.handler.data', 'Idmkr\Adwords\Handlers\Generation\GenerationDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.adgroup.handler.event', 'Idmkr\Adwords\Handlers\Generation\GenerationEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.adgroup.validator', 'Idmkr\Adwords\Validator\Generation\GenerationValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerKeyword()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\Keyword\KeywordRepositoryInterface',
            'Idmkr\Adwords\Repositories\Keyword\KeywordRepository'
        );
        $this->bindIf('idmkr.adwords.adgroup', 'Idmkr\Adwords\Repositories\Keyword\KeywordRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.adgroup.handler.data', 'Idmkr\Adwords\Handlers\Keyword\KeywordDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.adgroup.handler.event', 'Idmkr\Adwords\Handlers\Keyword\KeywordEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.adgroup.validator', 'Idmkr\Adwords\Validator\Keyword\KeywordValidator');
    }

    /**
     * {@inheritDoc}
     */
    public function registerUser()
    {
        // Register the repository
        $this->bindIf(
            'Idmkr\Adwords\Repositories\User\UserRepositoryInterface',
            'Idmkr\Adwords\Repositories\User\UserRepository'
        );
        $this->bindIf('idmkr.adwords.user', 'Idmkr\Adwords\Repositories\User\UserRepository');

        // Register the data handler
        $this->bindIf('idmkr.adwords.user.handler.data', 'Idmkr\Adwords\Handlers\User\UserDataHandler');

        // Register the event handler
        $this->bindIf('idmkr.adwords.user.handler.event', 'Idmkr\Adwords\Handlers\User\UserEventHandler');

        // Register the validator
        $this->bindIf('idmkr.adwords.user.validator', 'Idmkr\Adwords\Validator\User\UserValidator');
    }

}
