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
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
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
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\FeedItemCollection;
use Idmkr\Adwords\Iterators\AdGroupIterator;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Operator;

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Symfony\Component\Finder\Finder;

class FeedRepository extends AdwordsRepository implements FeedRepositoryInterface
{

    use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

    /**
     * The Data handler.
     *
     * @var \Idmkr\Adwords\Handlers\Feed\FeedDataHandlerInterface
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        parent::__construct();

        $this->setContainer($app);

        $this->setDispatcher($app['events']);

        $this->data = $app['idmkr.adwords.feed.handler.data'];

        $this->setValidator($app['idmkr.adwords.feed.validator']);
    }

    /**
     * @param AdWordsUser $user
     * @param             $name
     * @return null|AdCustomizerFeed
     */
    public function findByName(AdWordsUser $user, $name)
    {
        return $this->findByPredicate($user, new \Predicate("FeedName", "EQUALS", $name));
    }

    /**
     * @param AdWordsUser $user
     * @param             $id
     * @return null|AdCustomizerFeed
     */
    public function findById(AdWordsUser $user, $id)
    {
        return $this->findByPredicate($user, new \Predicate("FeedId", "EQUALS", $id));
    }

    /**
     * @param AdWordsUser $user
     * @param             $predicate
     * @return null|AdCustomizerFeed
     */
    private function findByPredicate(AdWordsUser $user, $predicate)
    {
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var AdCustomizerFeedService $adCustomizerFeedService */
        $adCustomizerFeedService = $user->GetService('AdCustomizerFeedService',
            $this->version);

        $selector = new \Selector(
            ["FeedName","FeedStatus","FeedAttributes"], $predicate
        );

        /** @var \AdCustomizerFeedPage $adCustomizerFeedPage */
        $adCustomizerFeedPage = $adCustomizerFeedService->get($selector);

        if(!$adCustomizerFeedPage->totalNumEntries)
            return null;

        foreach($adCustomizerFeedPage->entries as $adCustomizerFeed) {
            if($adCustomizerFeed->feedStatus == "ENABLED") {
                return $adCustomizerFeed;
            }
        }
        return null;
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
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var AdCustomizerFeedService $adCustomizerFeedService */
        $adCustomizerFeedService = $user->GetService('AdCustomizerFeedService',
            $this->version);

        $operation = new AdCustomizerFeedOperation();
        $operation->operand = $feed;
        $operation->operator = 'REMOVE';

        /** @var AdCustomizerFeedReturnValue $adCustomizerFeedReturn */
        $adCustomizerFeedReturn = $adCustomizerFeedService->mutate([$operation]);

        return $adCustomizerFeedReturn->value ? $adCustomizerFeedReturn->value[0] : null;
    }

    /**
     * @param AdWordsUser $user
     * @param  int           $feedId
     * @return FeedItem[]|null
     */
    public function findItemsByFeedId(AdWordsUser $user, $feedId)
    {
        // Get the AdCustomizerFeedService, which loads the required classes.
        /** @var FeedItemService $feedItemService */
        $feedItemService = $user->GetService('FeedItemService',
            $this->version);

        $selector = new \Selector(
            ["FeedItemId","Status","AttributeValues"],
            [
                new \Predicate("FeedId", "EQUALS", $feedId),
                new \Predicate("Status", "EQUALS", 'ENABLED'),
            ]
        );

        /** @var \FeedItemPage $feedItemPage */
        $feedItemPage = $feedItemService->get($selector);

        if(!$feedItemPage->totalNumEntries)
            return null;

        return $feedItemPage->entries;
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
            $this->version);

        $attributes = [];

        foreach($feedAttributes as $attribute) {
            $attributes[] = $this->buildCustomizerFeedAttribute($attribute, 'STRING');
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

        // Add the feed mapping
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
            $this->version);

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

    private function buildCustomizerFeedAttribute($name, $type)
    {
        $attribute = new AdCustomizerFeedAttribute();
        $attribute->name = $name;
        $attribute->type = $type;

        return $attribute;
    }

    /**
     * Creates FeedItems with the values to use in ad customizations for each ad
     * group in adGroupIds
     *
     * @param AdGroupCollection  $adGroups
     * @param FeedItemCollection $feedItems
     * @param callable           $getAdGroupFeedItem
     * @param string             $operator
     *
     * @return array
     */
    public function buildAdGroupsItemsOperations(
        AdGroupCollection $adGroups,
        FeedItemCollection $feedItems,
        $operator = "ADD"
    ){
        if($feedItems->isEmpty()) {
            return [];
        }

        // Get the FeedItemService, which loads the required classes.
        $this->requireService('FeedItemService');

        $operations = array();

        /** @var \AdGroup $adGroup */
        foreach($adGroups as $adGroup) {
            // If there's no feed item found for this adGroup, we'll pass
            // Todo: fix the need for an adgroup presence check
            if(!isset($feedItems[$adGroup->name])) {
                continue;
            }
            /** @var FeedItem $feedItem */
            $feedItem = $feedItems[$adGroup->name];

            $adGroupTargeting = new FeedItemAdGroupTargeting();
            $adGroupTargeting->TargetingAdGroupId = $adGroup->id;
            $feedItem->adGroupTargeting = $adGroupTargeting;

            if(!isset($feedItem)) {
                throw new InvalidArgumentException("Feed Item for AdGroup not found : $adGroup->name\n");
            }
            $operation = new FeedItemOperation();
            $operation->operator = $operator;
            $operation->operand = $feedItem;
            $operations[] = $operation;
        }

        return $operations;
    }
}
