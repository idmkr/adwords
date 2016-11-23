<?php namespace Idmkr\Adwords\Repositories\Campaigns;

use Campaign;
use CampaignOperation;
use CampaignReturnValue;
use CampaignService;
use AdWordsConstants;
use Idmkr\Adwords\Collections\CampaignCollection;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Cartalyst\Support\Traits;
use Error;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

class CampaignsRepository extends AdwordsRepository implements CampaignsRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Idmkr\Adwords\Handlers\Campaigns\CampaignsDataHandlerInterface
	 */
	protected $data;


	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
        parent::__construct();

		$this->setContainer($app);

		$this->setDispatcher($app['events']);

		$this->data = $app['idmkr.adwords.campaigns.handler.data'];

		$this->setValidator($app['idmkr.adwords.campaigns.validator']);
	}


	/**
	 * {@inheritDoc}
	 */
	public function findAll(AdWordsUser $user)
	{

        // Log every SOAP XML request and response.
        $user->LogAll();

        /** @var CampaignService $campaignService */
        $campaignService = $user->GetService('CampaignService', 'v201609');

        $query = 'SELECT Id, Name ORDER BY Name';

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

        return $campaigns;
	}

    /**
     * @param AdWordsUser $user
     * @param             $campaignId
     *
     * @return Campaign|null
     */
    public function find(AdWordsUser $user, $campaignId, $everyField = false)
    {
        // Get the CampaignService, which loads the required classes.
        /** @var CampaignService $campaignService */
        $campaignService = $user->GetService('CampaignService',
            $this->version);

        if($everyField) {
            $fields = [
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
        } else {
            $fields = ["Name","Id", "Status"];
        }

        $selector = new \Selector($fields, new \Predicate("Id", "EQUALS", $campaignId));

        /** @var \CampaignPage $campaignPage */
        $campaignPage = $campaignService->get($selector);

        if(!$campaignPage->totalNumEntries)
            return null;

        return $campaignPage->entries[0];
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
        if(is_int($campaign)) {
            $id = $campaign;
            if (!$campaign = $this->find($user, $id))
                throw new Exception("Campaign '$id' not found.");
        }
        // Get the CampaignService, which loads the required classes.
        /** @var CampaignService $campaignService */
        $campaignService = $user->GetService('CampaignService', $this->version);

        // https://developers.google.com/adwords/api/docs/reference/v201609/CampaignService.CampaignOperation
        // The REMOVE operator is not supported. To remove a campaign, set its status to REMOVED.
        $campaign->status = "REMOVED";
        $campaign->name .= $nameAppendix;

        $operation = new \CampaignOperation();
        $operation->operand = $campaign;
        $operation->operator = 'SET';


        /** @var \CampaignReturnValue $campaignReturn */
        $campaignReturn = $campaignService->mutate([$operation]);

        return $campaignReturn->value ? $campaignReturn->value[0] : null;
    }
}
