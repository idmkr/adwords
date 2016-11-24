<?php namespace Idmkr\Adwords\Operations\Builders\FeedItem;

use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\FeedItemCollection;
use Idmkr\Adwords\Operations\Builders\Builder;
use Idmkr\Adwords\Repositories\Feed\FeedRepository;

class FeedItemBuilder extends Builder
{
    protected $adwordsFeed;

    /**
     * FeedItemBuilder constructor.
     *
     * @param \AdCustomizerFeed      $adwordsFeed
     */
    public function __construct(\AdCustomizerFeed $adwordsFeed)
    {
        $this->adwordsFeed = $adwordsFeed;
    }

    /**
     * @param AdGroupCollection $adGroups
     *
     * @return \Operation[]
     */
    public function build($adGroups)
    {
        /** @var FeedRepository $adwordsFeeds */
        $adwordsFeeds = app('idmkr.adwords.feed');

        $feedItemsByAdGroupId = $this->buildFeedItemsByAdGroupId($adGroups);

        return $adwordsFeeds->buildAdGroupsItemsOperations($adGroups, $feedItemsByAdGroupId);
    }

    protected function buildFeedItemsByAdGroupId(AdGroupCollection $adGroups) : FeedItemCollection
    {
        $feedItemsCollection = new FeedItemCollection($this->adwordsFeed);

        foreach($this->getData() as $data) {
            $feedItemsCollection[$this->generateAdGroupName($data)] = $data;
        }

        return $feedItemsCollection;
    }
}