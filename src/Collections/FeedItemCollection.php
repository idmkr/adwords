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
        $this->feed = $feed;
        $this->dataHandler = $this->getDataHandler();
        parent::__construct($feedItems);
    }

    /**
     * @param callable|string $key
     *
     * @return static
     */
    public function keyBy($key)
    {
        $keyed = new static($this->feed);
        foreach($this->all() as $item) {
            $attributes = $this->dataHandler->getItemAttributes($item);
            if(is_callable($key)) {
               $keyed[$key($attributes, $item)] = $item;
            }
            else {

                $keyed[$attributes[$key]] = $item;
            }
        }
        return $keyed;
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

    /**
     * @return FeedItemDataHandler
     */
    protected function getDataHandler() : DataHandler
    {
        return app("idmkr.adwords.feeditem.handler.data", [$this->feed]);
    }
}