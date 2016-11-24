<?php namespace Idmkr\Adwords\Operations\Builders\Ad;

use Idmkr\Adwords\Collections\ExpandedTextAdCollection;
use Idmkr\Adwords\Repositories\Ad\AdRepository;
use Illuminate\Support\Collection;

class CustomizedAdBuilder extends AdBuilder
{
    /**
     * @var Collection
     */
    private $adTemplates;
    private $feedName;

    public function __construct(array $adTemplates, $feedName)
    {
        $this->adTemplates = collect($adTemplates);
        $this->feedName = $feedName;
    }

    /**
     * @param mixed $adGroups
     *
     * @return array
     */
    public function build($adGroups)
    {
        /** @var AdRepository $adwordsAds */
        $adwordsAds = app('idmkr.adwords.ad');

        return $adwordsAds->buildAdGroupOperations($adGroups, $this->getAds());
    }

    private function getAds() : ExpandedTextAdCollection
    {
        $adTemplates = $this->adTemplates;

        return $this->dataMapByAdGroup($adTemplates, function ($feedItem, $adTemplate) {
            // Does this ad template pass validation ?
            while(!$this->adPassValidation($adTemplate, $feedItem)) {
                $adTemplate = $adTemplate["alternative"];
            }

            return $this->renameVarsToAdwordsVars($adTemplate, $this->feedName);
        }, new ExpandedTextAdCollection());
    }
}