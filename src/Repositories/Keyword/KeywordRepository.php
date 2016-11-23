<?php namespace Idmkr\Adwords\Repositories\Keyword;

use AdGroupCriterionOperation;
use BiddableAdGroupCriterion;
use Cartalyst\Support\Traits;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\KeywordCollection;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use Keyword;
use Symfony\Component\Finder\Finder;
use Traversable;

class KeywordRepository extends AdwordsRepository implements KeywordRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Idmkr\Adwords\Handlers\Keyword\KeywordDataHandlerInterface
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

		$this->data = $app['idmkr.adwords.keyword.handler.data'];

		$this->setValidator($app['idmkr.adwords.keyword.validator']);
	}

	/**
	 * @param AdGroupCollection $adGroups
	 * @param KeywordCollection       $keywordsDataByAdGroupName
	 *
	 * @return \Operation[]
     */
    public function buildAdGroupOperations(AdGroupCollection $adGroups, KeywordCollection $keywordsDataByAdGroupName)
	{
        $this->requireService("AdGroupCriterionService");

        $adGroupCriteriaOperations = array();

        // Create AdGroupCriterionOperations to add keywords.
        foreach ($adGroups as $adGroup) {
            $adGroupId = $adGroup->id;
            $keywordsData = $keywordsDataByAdGroupName[$adGroup->name];

            foreach($keywordsData as $keyword) {
                // Create BiddableAdGroupCriterion.
                $biddableAdGroupCriterion = new BiddableAdGroupCriterion();
                $biddableAdGroupCriterion->adGroupId = $adGroupId;
                $biddableAdGroupCriterion->criterion = $keyword;

                // Create AdGroupCriterionOperation.
                $operation = new AdGroupCriterionOperation();
                $operation->operand = $biddableAdGroupCriterion;
                $operation->operator = 'ADD';

                // Add to list.
                $adGroupCriteriaOperations[] = $operation;
            }
        }
        return $adGroupCriteriaOperations;

	}

}
  