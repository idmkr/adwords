<?php namespace Idmkr\Adwords\Handlers\Ad;

use Ad;
use Idmkr\Adwords\Handlers\DataHandler;
use Illuminate\Database\Eloquent\Model;

class AdGroupAdDataHandler extends DataHandler
{
    public function prepareModel($data)
    {
        $adGroupAd = new \AdGroupAd();

        /** @var ExpandedTextAdDataHandler $expandedTextAdHandler */
        $expandedTextAdHandler = app('idmkr.adwords.expandedtextad.handler.data');
        $adGroupAd->ad = $expandedTextAdHandler->prepare($data);

        $adGroupAd->adGroupId = $data["adGroupId"];
        $adGroupAd->status = $data["status"];
        $adGroupAd->approvalStatus = $data["approvalStatus"];
        $adGroupAd->baseCampaignId = $data["baseCampaignId"];
        
        return $adGroupAd;
    }
}
