<?php namespace Idmkr\Adwords\Collections;

use Campaign;
use Idmkr\Adwords\Handlers\Campaigns\CampaignDataHandler;

/**
 * Class CampaignCollection
 *
 * @package Idmkr\Adwords\Collections
 * @method Campaign current()
 */
class CampaignCollection extends AdwordsCollection
{
    protected $dataHandler = CampaignDataHandler::class;
}