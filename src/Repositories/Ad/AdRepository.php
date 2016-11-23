<?php namespace Idmkr\Adwords\Repositories\Ad;

use AdGroupAd;
use AdGroupAdOperation;
use AdGroupAdService;
use AdGroupOperation;
use Cartalyst\Support\Traits;
use ExpandedTextAd;
use Idmkr\Adwords\Collections\AdCollection;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Idmkr\Template\Repositories\Templategroupeannonces\TemplategroupeannoncesRepository;
use Illuminate\Container\Container;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Symfony\Component\Finder\Finder;
use Traversable;

class AdRepository extends AdwordsRepository implements AdRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Idmkr\Adwords\Handlers\Ad\AdDataHandlerInterface
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

		$this->data = $app['idmkr.adwords.ad.handler.data'];

		$this->setValidator($app['idmkr.adwords.ad.validator']);
	}


	/**
	 * Creates text ads that use ad customizations for the specified ad group IDs.
	 *
	 * @param AdGroupCollection $adGroups the IDs of the ad groups to target with the FeedItem
	 * @param AdCollection $adsByAdGroupName  the data
     *
     * @return AdGroupAd[]
	 */
	public function buildAdGroupOperations(AdGroupCollection $adGroups, $adsByAdGroupName)
    {
        $this->requireService("AdGroupAdService");
        $operations = array();

        try {

            foreach ($adGroups as $adGroup) {
                $ads = $adsByAdGroupName[$adGroup->name];

                foreach($ads as $expandedTextAd) {
                    $adGroupAd = new AdGroupAd();
                    $adGroupAd->adGroupId = $adGroup->id;

                    $adGroupAd->ad = $expandedTextAd;

                    $operation = new AdGroupAdOperation();
                    $operation->operator = 'ADD';
                    $operation->operand = $adGroupAd;

                    $operations[] = $operation;

                }
            }

        }
        catch (\Exception $e) {
            dd($e);
        }

		return $operations;
	}



}
