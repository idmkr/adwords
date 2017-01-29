<?php namespace Idmkr\Adwords\Handlers\Campaigns;

use BiddingStrategyConfiguration;
use Budget;
use Campaign;
use Idmkr\Adwords\Handlers\Budget\BudgetDataHandler;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;
use ManualCpcBiddingScheme;
use TempIdGenerator;

class CampaignDataHandler extends DataHandler
{
    use RequireAdWordsServiceTrait;
    
    private $budgetHandler;

    public function __construct()
    {
        $this->budgetHandler = new BudgetDataHandler();
        $this->requireService('CampaignService');
    }

    public function prepareString(string $id) : Campaign
    {
        return $this->prepareInt((int)$id);

    }

    public function prepareInt(int $id) : Campaign
	{
		$campaign = new Campaign();
		$campaign->id = $id;

		return $campaign;
	}
 
	public function prepareArray(array $data) : Campaign
	{
		$this->requireData($data, ['name', 'budget']);

		// Create campaign.
		$campaign = new Campaign();
		$campaign->name = $data['name'];
		$campaign->advertisingChannelType = 'SEARCH';
        
        if(!isset($data["id"])) {
            $this->requireService("Util/TempIdGenerator", false);
            $campaign->id = TempIdGenerator::Generate();
        }

		// Set shared budget (required).
        $campaign->budget = $this->budgetHandler->prepare([
            'name' => $data['name'],
            'amount' => $data['budget']
        ]);

		// Set bidding strategy (required).
		$biddingStrategyConfiguration = new BiddingStrategyConfiguration();
		$biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';

		// You can optionally provide a bidding scheme in place of the type.
		$biddingScheme = new ManualCpcBiddingScheme();
		$biddingScheme->enhancedCpcEnabled = false;
		$biddingStrategyConfiguration->biddingScheme = $biddingScheme;

		$campaign->biddingStrategyConfiguration = $biddingStrategyConfiguration;

        if(isset($data['status'])) {
            $campaign->status = $data['status'];
        }

		return $campaign;
	}
}
