<?php namespace Idmkr\Adwords\Collections;

use Idmkr\Adwords\Handlers\Operation\OperationDataHandler;
use Keyword;


/**
 * Class KeywordCollection
 *
 * @package Idmkr\Keywordwords\Collections
 */
class OperationCollection extends AdwordsCollection
{
    protected $dataHandler = OperationDataHandler::class;
}