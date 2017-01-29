<?php namespace Idmkr\Adwords\Collections;

use FeedItem;
use FeedItemAdGroupTargeting;
use FeedItemAttributeValue;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Handlers\Feed\FeedItemDataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;


/**
 * Class FeedItemCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class FeedItemCollection extends AdwordsCollection
{
    private $feed;
    /**
     * @var FeedItem[]
     */
    public $items;

    /**
     * FeedItemCollection constructor.
     *
     * @param array             $feedItems
     * @param \AdCustomizerFeed $feed
     */
    public function __construct(\AdCustomizerFeed $feed, array $feedItems = [])
    {
        parent::__construct($feedItems);
        $this->feed = $feed;
    }

    public function keyBy($key)
    {
        $keyed = new static($this->feed);
        foreach($this->all() as $item) {
            $attributes = $this->getItemAttributes($item);
            if(is_callable($key)) {
               $keyed[$key($attributes)] = $item;
            }
            else {

                $keyed[$attributes[$key]] = $item;
            }
        }
        return $keyed;
    }

    /**
     * @param mixed $feedItem
     *
     * @return array
     */
    public function getItemAttributes($feedItem) : array
    {
        if(is_int($feedItem)) {
            $feedItem = $this->get($feedItem);
        }

        $attrNames = collect($this->feed->feedAttributes)->keyBy(function (\AdCustomizerFeedAttribute $feedAttribute) {
            return $feedAttribute->id;
        })->map(function (\AdCustomizerFeedAttribute $feedAttribute) {
            return $feedAttribute->name;
        });
        $attrValues = collect($feedItem->attributeValues)->keyBy(function (\FeedItemAttributeValue $feedItemAttributeValue) {
            return $feedItemAttributeValue->feedAttributeId;
        })->map(function (\FeedItemAttributeValue $feedItemAttributeValue) {
            return $feedItemAttributeValue->stringValue;
        });

        $attrs = [];
        foreach($attrNames as $attrId => $attrName) {
            $attrs[$attrName] = $attrValues[$attrId] ?? null;
        }

        return $attrs;
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed  $items
     * @return static
     */
    public function clone($items = [])
    {
        return new static($this->feed, $items);
    }

    protected function getItemProperty($feedItem, $property, $default = '')
    {
        $attributes = $this->getItemAttributes($feedItem);

        if(is_callable($property)) {
            return $property($attributes);
        }
        else if(isset($attributes[$property])) {
            return $attributes[$property];
        }

        return $default;
    }

    /**
     * @return FeedItemDataHandler
     */
    protected function getDataHandler() : DataHandler
    {
        return app("idmkr.adwords.feeditem.handler.data", [$this->feed]);
    }
}