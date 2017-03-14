<?php namespace Idmkr\Adwords\Repositories\Campaigns;

use Campaign;
use CampaignOperation;
use CampaignReturnValue;
use CampaignService;
use AdWordsConstants;
use Idmkr\Adwords\Collections\CampaignCollection;
use Idmkr\Adwords\Handlers\Campaigns\CampaignDataHandler;
use Idmkr\Adwords\Handlers\Campaigns\CampaignsDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Idmkr\Adwords\Repositories\Budget\BudgetRepository;
use Idmkr\Adwords\Repositories\Budget\BudgetRepositoryInterface;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Cartalyst\Support\Traits;
use Error;
use Exception;
use Illuminate\Container\Container;

class CampaignsRepository extends AdwordsRepository
{
    /**
     * @var BudgetRepositoryInterface
     */
    private $budgets;

    /**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
        parent::__construct($app);
        $this->budgets = app('idmkr.adwords.budget');

        $this->requireService('CampaignService');
    }


    /**
     * @param AdWordsUser $user
     *
     * @return CampaignCollection
     */
    public function findAll(AdWordsUser $user) : CampaignCollection
	{
        // Log every SOAP XML request and response.
        //$user->LogAll();

        /** @var CampaignService $campaignService */
        $campaignService = $user->GetService('CampaignService', 'v201609');

        return $this->container['cache']->remember('idmkr.adwords.campaigns.all.'.$user->GetClientCustomerId(), 60, function() use($campaignService) {

            $query = "SELECT Id, Name WHERE Status IN ['ENABLED','PAUSED'] ORDER BY Name";

            // Create paging controls.
            $offset = 0;

            $campaigns = new CampaignCollection();

            do {
                $pageQuery = sprintf('%s LIMIT %d,%d', $query, $offset,
                    AdWordsConstants::RECOMMENDED_PAGE_SIZE);
                // Make the query request.
                $page = $campaignService->query($pageQuery);

                // Display results.
                if (isset($page->entries)) {
                    foreach ($page->entries as $campaign) {
                        $campaigns->push($campaign);
                    }
                } else {
                    // No campaign
                }

                // Advance the paging offset.
                $offset += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
            } while ($page->totalNumEntries > $offset);

            return new CampaignCollection($campaigns);
        });
	}

    /**
     * @param AdWordsUser $user
     * @param             $campaignId
     *
     * @return Campaign|null
     */
    public function find(AdWordsUser $user, $campaignId, $everyField = false)
    {
        return $this->container['cache']->rememberForever('idmkr.adwords.campaigns.'.$campaignId, function() use($user, $everyField, $campaignId) {
            if ($everyField) {
                $fields = $this->getFields();
            } else {
                $fields = ["Name", "Id", "Status"];
            }

            $campaigns = $this->get($user, $fields, $campaignId);

            return empty($campaigns) ? null : $campaigns[0];
        });
    }

    /**
     * @param AdWordsUser $user
     * @param             $name
     *
     * @return Campaign|null
     */
    public function findByName(AdWordsUser $user, $name)
    {
        return $this->first($user, ["Name", "Id", "Status"], new \Predicate("Name", "EQUALS", $name));
    }

    /**
     * @param AdWordsUser $user
     * @param             $campaign
     *
     * @return Campaign
     */
    public function create(AdWordsUser $user, $campaign) : Campaign
    {
        $campaign = $this->getDataHandler()->prepare($campaign);

        if(!$campaign->budget->budgetId) {
            $budget = $this->budgets->create($user, $campaign->budget);
            $campaign->budget->budgetId = $budget->budgetId;
        }

        return $this->mutate($user, $campaign);
    }

    /**
     * @param AdWordsUser   $user
     * @param \Campaign|int $campaign
     * @param string        $nameAppendix
     *
     * @return bool|Campaign
     * @throws Exception
     */
    public function delete($user, $campaign, $nameAppendix = '')
    {
        $campaign = $this->getDataHandler()->prepare($campaign);

        if (!$campaign = $this->find($user, $campaign->id))
            throw new Exception("Campaign '$campaign->id' not found.");

        // https://developers.google.com/adwords/api/docs/reference/v201609/CampaignService.CampaignOperation
        // The REMOVE operator is not supported. To remove a campaign, set its status to REMOVED.
        $campaign->status = "REMOVED";
        $campaign->name .= $nameAppendix;

        return $this->mutate($user, $campaign, 'SET');
    }

    /**
     * @return string
     */
    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.campaign';
    }

    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return 'Campaign';
    }

    /**
     * @return CampaignsDataHandlerInterface
     */
    protected function getDataHandler() : CampaignDataHandler
    {
        return app('idmkr.adwords.campaigns.handler.data');
    }

    private function getFields()
    {
        return [
            "Name",
            "Id",
            "Status",
            "StartDate",
            "EndDate",
            "AdServingOptimizationStatus",
            "Settings",
            "AdvertisingChannelType",
            "AdvertisingChannelSubType",
            "Labels"
        ];
    }
}
