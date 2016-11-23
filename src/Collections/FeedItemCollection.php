<?php namespace Idmkr\Adwords\Collections;

use FeedItem;
use FeedItemAdGroupTargeting;
use FeedItemAttributeValue;


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

    /**
     * build a FeedItem
     *
     * @param array $data the attributes
     */
    protected function parseArrayItem(array $data) : FeedItem
    {
        $attributeValues = collect($data)->values()->map(function ($attr, $i){
            // Create the FeedItemAttributeValues for our text values.
            $attributeValue = new FeedItemAttributeValue();
            $attributeValue->feedAttributeId = $this->feed->feedAttributes[$i]->id;
            $attributeValue->stringValue = $attr;

            return $attributeValue;
        })->toArray();

        // Create the feed item and operation.
        $item = new FeedItem();
        $item->feedId = $this->feed->feedId;
        $item->attributeValues = $attributeValues;

        return $item;
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

    public function getItemAttributes(\FeedItem $feedItem) : array
    {
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
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);

        $items = array_map($callback, $this->items, $keys);

        return $this->clone(array_combine($keys, $items));
    }

    public function keys()
    {
        return $this->clone(array_keys($this->items));
    }

    public function flip()
    {
        return $this->clone(array_flip($this->items));
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
}