<?php namespace Idmkr\Adwords\Collections;

use Ad;
use Idmkr\Adwords\Handlers\Ad\AdGroupAdDataHandler;


/**
 * Class AdGroupAdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class AdGroupAdCollection extends AdwordsCollection
{
    protected $dataHandler = AdGroupAdDataHandler::class;
}