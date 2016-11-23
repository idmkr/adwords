<?php namespace Idmkr\Adwords\Collections;

use Campaign;
use Illuminate\Support\Arr;

/**
 * Class CampaignCollection
 *
 * @package Idmkr\Adwords\Collections
 * @method Campaign current()
 */
class CampaignCollection extends AdwordsCollection
{
    function parseIntItem(int $id) : Campaign
    {
        $campaign = new Campaign();
        $campaign->id = $id;

        return $campaign;
    }
}