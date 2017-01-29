<?php namespace Idmkr\Adwords\Collections;

use Ad;
use Idmkr\Adwords\Handlers\Ad\AdDataHandler;


/**
 * Class AdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class AdCollection extends AdwordsCollection
{
    protected $dataHandler = AdDataHandler::class;
}