<?php namespace Idmkr\Adwords\Repositories\Campaigns;

use Campaign;
use Idmkr\Adwords\Collections\CampaignCollection;
use LaravelGoogleAds\AdWords\AdWordsUser;

interface CampaignsRepositoryInterface
{
    public function create(AdWordsUser $user, $campaign) : Campaign;
    public function find(AdWordsUser $user, $campaignId, $everyField = false) : Campaign;
    public function findAll(AdWordsUser $user) : CampaignCollection;
    public function delete($user, $campaign, $nameAppendix = '');
}
