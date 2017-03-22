<?php namespace Idmkr\Adwords\Repositories\Feed;

use AdCustomizerFeedAttribute;
use AdCustomizerFeedOperation;
use AdCustomizerFeedReturnValue;
use AdCustomizerFeedService;
use AdGroupAd;
use AdGroupAdOperation;
use AdGroupAdService;
use AdGroupOperation;
use AttributeFieldMapping;
use CustomerFeed;
use Exception;
use ExpandedTextAd;
use FeedItemAdGroupTargeting;
use FeedItemAttributeValue;
use FeedItemOperation;
use AdCustomizerFeed;
use FeedItemService;
use FeedMapping;
use FeedMappingOperation;
use FeedMappingService;
use FeedOperation;
use FeedService;
use FeedItem;
use Idmkr\Adwords\Collections\FeedItemCollection;
use Idmkr\Adwords\Handlers\Feed\FeedDataHandlerInterface;
use Idmkr\Adwords\Handlers\Feed\FeedItemDataHandler;
use Idmkr\Adwords\Handlers\Feed\FeedItemDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Operator;

use Cartalyst\Support\Traits;
use LaravelGoogleAds\AdWords\AdWordsUser;

class FeedRepository extends AdwordsRepository
{
    /** 
     * @param AdWordsUser $user
     * @param             $name
     * @return null|AdCustomizerFeed
     */
    public function findByName(AdWordsUser $user, $name)
    {
        return $this->find($user, [
            new \Predicate("FeedName", "EQUALS", $name),
            new \Predicate("FeedStatus", "EQUALS", 'ENABLED'),
        ]);
    }

    /**
     * @param AdWordsUser $user
     * @param             $id
     * @return null|AdCustomizerFeed
     */
    public function findById(AdWordsUser $user, $id)
    {
        return $this->find($user, new \Predicate("FeedId", "EQUALS", $id));
    }

    /**
     * @param AdWordsUser $user
     * @param             $predicate
     * @return null|AdCustomizerFeed
     */
    public function find(AdWordsUser $user, $predicate)
    {
        if(is_numeric($predicate)) {
            $predicate = new \Predicate("FeedId","EQUALS",$predicate);
        }
        return $this->get($user, ["FeedName","FeedStatus","FeedAttributes"], $predicate)[0] ?? null;
    }

    /**
     * @param AdWordsUser $user
     * @param \AdCustomizerFeed|int $feed
     * @return AdCustomizerFeed|bool
     */
    public function delete($user, $feed)
    {
        if(is_int($feed)) {
            $id = $feed;
            if (!$feed = $this->findById($user, $id))
                throw new Exception("AdWords Feed $id not found.");
        }

        return $this->mutate($user, $feed, 'REMOVE');
    }

    /**
     * @param AdWordsUser $user
     * @param  \AdCustomizerFeed      $feed
     * @return FeedItemCollection|null
     */
    public function findItemsByFeed(AdWordsUser $user, \AdCustomizerFeed $feed)
    {
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var FeedItemService $feedItemService */
        $feedItemService = $user->GetService('FeedItemService',
            $this->getAdwordsApiVersion());

        $selector = new \Selector(
            ["FeedItemId","Status","AttributeValues", "TargetingAdGroupId"],
            [
                new \Predicate("FeedId", "EQUALS", $feed->feedId),
                new \Predicate("Status", "EQUALS", 'ENABLED'),
            ]
        );

        /** @var \FeedItemPage $feedItemPage */
        $feedItemPage = $feedItemService->get($selector);

        return new FeedItemCollection($feed, $feedItemPage->entries ?: []);
    }

    /**
     * Creates a new Feed for AdCustomizerFeed.
     *
     * @param AdWordsUser $user the user to run the example with
     * @param string $feedName the name of the new AdCustomizerFeed
     * @param array $feedAttributes the feed attributes
     * @return AdCustomizerFeed
     */
    public function create(AdWordsUser $user, $feedName, $feedAttributes) {
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var AdCustomizerFeedService $adCustomizerFeedService */
        $adCustomizerFeedService = $user->GetService('AdCustomizerFeedService',
            $this->getAdwordsApiVersion());

        $attributes = [];

        foreach($feedAttributes as $attribute) {
            $attributes[] = $this->buildCustomizerFeedAttribute($attribute);
        }

        $customizerFeed = new AdCustomizerFeed();
        $customizerFeed->feedName = $feedName;
        $customizerFeed->feedAttributes = $attributes;

        $feedOperation = new AdCustomizerFeedOperation();
        $feedOperation->operand = $customizerFeed;
        $feedOperation->operator = 'ADD';

        $operations = array($feedOperation);

        // Add the feed.
        $result = $adCustomizerFeedService->mutate($operations);
        $addedFeed = $result->value[0];

        // Add the feed mapping ?!
        //$this->createFeedMappingPlaceholder($user, $addedFeed);

        return $addedFeed;
    }

    /**
     * Creates a new FeedMapping for a specific feed
     *
     * @param AdWordsUser $user the user to run the example with
     * @param \Feed $feed the name of the new AdCustomizerFeed
     * @param array $feedAttributes the feed attributes
     * @return \FeedMapping
     */
    public function createFeedMappingPlaceholder(AdWordsUser $user, AdCustomizerFeed $feed)
    {
        // Get the FeedMappingService, which loads the required classes.
        /** @var FeedMappingService $feedMappingService */
        $feedMappingService = $user->GetService('FeedMappingService',
            $this->getAdwordsApiVersion());

        $feedMapping = new FeedMapping();
        $feedMapping->placeholderType = 10;
        $feedMapping->feedId = $feed->feedId;
        foreach($feed->feedAttributes as $feedAttribute) {
            $feedMapping->attributeFieldMappings[] =
                $this->buildFeedMappingAttributePlaceholder($feedAttribute->id, 5);
        }

        $feedOperation = new FeedMappingOperation();
        $feedOperation->operand = $feedMapping;
        $feedOperation->operator = 'ADD';

        $result = $feedMappingService->mutate($feedOperation);
        $addedFeedMapping = $result->value[0];

        return $addedFeedMapping;
    }

    private function buildFeedMappingAttributePlaceholder($id, $fieldId)
    {
        $attribute = new AttributeFieldMapping();
        $attribute->feedAttributeId = $id;
        $attribute->fieldId = $fieldId;

        return $attribute;
    }

    private function buildCustomizerFeedAttribute($attr)
    {
        $attribute = new AdCustomizerFeedAttribute();
        $attribute->name = $attr["name"];
        $attribute->type = $attr["type"];

        return $attribute;
    }

    /**
     * Creates FeedItems with the values to use in ad customizations for each ad
     * group in adGroupIds
     *
     * @param \AdGroup  $adGroup
     * @param mixed $feedItem
     * @param string             $operator
     *
     * @return FeedItemOperation
     */
    public function buildAdGroupItemOperation(\AdGroup $adGroup, $feedItem, $operator = "ADD")
    {
        // Get the FeedItemService, which loads the required classes.
        $this->requireService('FeedItemService');

        $adGroupTargeting = new FeedItemAdGroupTargeting();
        $adGroupTargeting->TargetingAdGroupId = $adGroup->id;
        $feedItem->adGroupTargeting = $adGroupTargeting;

        $operation = new FeedItemOperation();
        $operation->operator = $operator;
        $operation->operand = $feedItem;

        return $operation;
    }

    /**
     * @return string
     */
    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.feed';
    }

    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return 'AdCustomizerFeed';
    }

    protected function getItemDataHandler() : FeedItemDataHandler
    {
        return app('idmkr.adwords.feeditem.handler.data');
    }

    protected function getDataHandler() : FeedDataHandlerInterface
    {
        return app('idmkr.adwords.feed.handler.data');
    }
}
