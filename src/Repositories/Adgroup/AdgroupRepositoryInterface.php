<?php namespace Idmkr\Adwords\Repositories\Adgroup;

use AdGroup;
use LaravelGoogleAds\AdWords\AdWordsUser;

interface AdgroupRepositoryInterface 
{
    public function buildCampaignOperation(\Campaign $campaign, AdGroup $adGroup, $operator = 'ADD');
    public function findByCampaignId(AdWordsUser $adwordsUser, $campaign_id);
}
