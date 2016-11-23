<?php namespace Idmkr\Adwords\Collections;

use Ad;


/**
 * Class AdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class AdCollection extends AdwordsCollection
{
    /**
     * build an Ad
     *
     * @param array $data the attributes
     */
    public function parseArrayItem(array $data)
    {
        $expandedTextAd = new Ad();

        $expandedTextAd->headlinePart1 = $data["title1"];
        $expandedTextAd->headlinePart2 = $data["title2"];
        $expandedTextAd->description = $data["description"];
        $expandedTextAd->finalUrls = $data["url"];

        return $expandedTextAd;
    }
}