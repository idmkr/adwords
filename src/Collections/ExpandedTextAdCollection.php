<?php namespace Idmkr\Adwords\Collections;

use ExpandedTextAd;
use Idmkr\Adwords\Handlers\Ad\ExpandedTextAdDataHandler;


/**
 * Class AdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class ExpandedTextAdCollection extends AdCollection
{
    protected $dataHandler = ExpandedTextAdDataHandler::class;

}