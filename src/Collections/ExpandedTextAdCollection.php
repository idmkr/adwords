<?php namespace Idmkr\Adwords\Collections;

use ExpandedTextAd;


/**
 * Class AdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class ExpandedTextAdCollection extends AdCollection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * build an ExpandedTextAd
     *
     * @param array $data the attributes
     */
    public function parseArrayItem(array $data) : ExpandedTextAd
    {
        $expandedTextAd = new ExpandedTextAd();

        $expandedTextAd->headlinePart1 = $data["title1"];
        $expandedTextAd->headlinePart2 = $data["title2"];
        $expandedTextAd->description = $data["description"];
        $expandedTextAd->finalUrls = $data["url"];

        return $expandedTextAd;
    }
    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->items[] = $this->parseItem($value);
        } else {
            $this->items[$key] = $this->parseItem($value);
        }
    }
}