<?php namespace Idmkr\Adwords\Repositories\Keyword;

use AdGroupCriterionOperation;
use BiddableAdGroupCriterion;
use Cartalyst\Support\Traits;
use Idmkr\Adwords\Handlers\Keyword\KeywordDataHandler;
use Idmkr\Adwords\Handlers\Keyword\KeywordDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Container\Container;
use Keyword;

class KeywordRepository extends AdwordsRepository
{
    /**
     * @var KeywordDataHandlerInterface
     */
    protected $dataHandler;

    public function __construct(Container $app)
    {
        parent::__construct($app);

        $this->requireService("AdGroupCriterionService");
    }

    /**
	 * @param \AdGroup $adGroup
	 * @param mixed    $keyword
	 *
	 * @return AdGroupCriterionOperation
     */
    public function buildAdGroupOperation(\AdGroup $adGroup, $keyword)
	{
        // Create BiddableAdGroupCriterion.
        $biddableAdGroupCriterion = new BiddableAdGroupCriterion();
        $biddableAdGroupCriterion->adGroupId = $adGroup->id;
        $biddableAdGroupCriterion->criterion = $this->getDataHandler()->prepare($keyword);

        // Create AdGroupCriterionOperation.
        $operation = new AdGroupCriterionOperation();
        $operation->operand = $biddableAdGroupCriterion;
        $operation->operator = 'ADD';

        return $operation;

	}

    /**
     * @return string
     */
    protected function getEntityClassName() : string
    {
        return 'Keyword';
    }

    /**
     * @return string
     */
    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.keyword';
    }

    protected function getDataHandler() : KeywordDataHandler
    {
        return app('idmkr.adwords.keyword.handler.data');
    }
}
  