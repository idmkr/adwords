<?php namespace Idmkr\Adwords\Operations\Builders\AdGroup;

use AdGroup;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\CampaignCollection;
use Idmkr\Adwords\Operations\Builders\Builder;
use Idmkr\Adwords\Repositories\Adgroup\AdgroupRepository;

class AdGroupBuilder extends Builder
{
    /**
     * @param CampaignCollection $campaigns
     *
     * @return array
     */
    public function build($campaigns)
    {
        /** @var AdgroupRepository $adwordsAdGroups */
        $adwordsAdGroups = app('idmkr.adwords.adgroup');

        return $adwordsAdGroups->buildCampaignsOperations($campaigns, $this->buildFreshAdGroups());
    }

    /**
     * @return AdGroupCollection
     */
    protected function buildFreshAdGroups() : AdGroupCollection
    {
        $adGroups = new AdGroupCollection();
        foreach ($this->getData() as $item) {
            $adGroup = [
                "name" => $this->applyFeedVars($this->getAdGroupTemplate()["name"], $item),
                "bid" => $this->getAdGroupTemplate()["bid"]
            ];

            $adGroups->push($adGroup);
        }
        return $adGroups;
    }
}