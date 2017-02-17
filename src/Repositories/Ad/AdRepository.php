<?php namespace Idmkr\Adwords\Repositories\Ad;

use AdGroup;
use AdGroupAd;
use AdGroupAdOperation;
use AdGroupAdService;
use AdGroupOperation;
use Cartalyst\Support\Traits;
use ExpandedTextAd;
use Idmkr\Adwords\Handlers\Ad\ExpandedTextAdDataHandler;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Idmkr\Adwords\Collections\AdGroupAdCollection;

class AdRepository extends AdwordsRepository
{
    public function __construct(Container $app)
    {
        parent::__construct($app);

        $this->requireService("AdGroupAdService");
    }

	/**
	 * Creates text ads that use ad customizations for the specified ad group IDs.
	 *
	 * @param mixed $adGroup the IDs of the ad groups to target with the FeedItem
	 * @param mixed     $ad
	 * @param mixed     $adGroupAd
	 *
	 * @return AdGroupAdOperation
	 *
	 */
	public function buildAdGroupOperation($adGroup, $adData, $operator = 'ADD')
    {
        $adGroup = $this->getAdGroupDataHandler()->prepare($adGroup);
        $expandedTextAd = $this->getDataHandler()->prepare($adData);

        $adGroupAd = new AdGroupAd();
        $adGroupAd->adGroupId = $adGroup->id;
        $adGroupAd->ad = $expandedTextAd;

        if(isset($adGroupAdData["enabled"])) {
            $adGroupAd->status = $adGroupAdData["enabled"] == 1 ? "ENABLED" : "PAUSED";
        }

		return $this->fillOperation(new AdGroupAdOperation, $adGroupAd, $operator);
	}

    /**
     * @return string
     */
    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.ad';
    }

    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return 'AdGroupAd';
    }

    protected function getDataHandler() : ExpandedTextAdDataHandler
    {
        return app('idmkr.adwords.expandedtextad.handler.data');
    }

    /**
     * @param AdWordsUser $adwordsUser
     * @param int         $campaign_id
     *
     * @return AdGroupAdCollection
     */
    public function findAdGroupAdByCampaignId(AdWordsUser $adwordsUser, $campaign_id) : AdGroupAdCollection
    {
        return new AdGroupAdCollection(
            $this->get($adwordsUser,
                // Select
                ["AdGroupId", "HeadlinePart1","HeadlinePart2", "Description", "Path1", "Path2","CreativeFinalUrls"],
                // Where
                [new \Predicate("BaseCampaignId", "EQUALS", $campaign_id)],
                // Order By
                new \OrderBy('Id'),
                // Using a specific service
                'AdGroupAdService'
            )
        );
    }
}
