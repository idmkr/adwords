<?php namespace Idmkr\Adwords\Repositories;

use AdWordsConstants;
use Budget;
use BudgetOperation;
use Idmkr\Adwords\Handlers\Adgroup\AdgroupDataHandler;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;
use Illuminate\Container\Container;
use LaravelGoogleAds\AdWords\AdWordsUser;
use Cartalyst\Support\Traits;
use Operation;

abstract class AdwordsRepository
{
    use RequireAdWordsServiceTrait, Traits\ContainerTrait, Traits\EventTrait;

    /**
     * @var array
     */
    private $eventNamesByOperator = [
        'ADD' => [
            'before' => 'creating',
            'after' => 'created',
        ],
        'SET' => [
            'before' => 'updating',
            'after' => 'updated',
        ],
        'REMOVE' => [
            'before' => 'deleting',
            'after' => 'deleted',
        ],
    ];

    /**
     * @return string
     */
    abstract protected function getEventNamespace() : string;

    /**
     * @return string
     */
    abstract protected function getEntityClassName() : string;

    /**
     * @return DataHandler
     */
    abstract protected function getDataHandler();

    /*
     * AdwordsRepository constructor.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->setContainer($app);

        $this->setDispatcher($app['events']);
    }

    /**
     * Save adwords entities by mutating its state
     *
     * @param AdWordsUser $adWordsUser
     * @param             $entities
     * @param string      $operator
     *
     * @return mixed the results
     */
    public function mutate(AdWordsUser $adWordsUser, $entities, $operator = 'ADD')
    {
        // Get the Service, which loads the required classes.
        $service = $this->getService($adWordsUser);
        $operationClassName = $this->getOperationClassName();
        
        $operations = [];

        if(is_a($entities, $this->getEntityClassName())) {
            $entities = [$entities];
        }

        $entitiesCount = count($entities);

        foreach($entities as $entity) {
            // Fire the event
            if ($this->trigger('before', $operator, $entity)  === false) {
                continue;
            }
            
            $operation = new $operationClassName();
            $operation->operand = $this->getDataHandler()->prepare($entity);
            $operation->operator = $operator;
            $operations[] = $operation;
        }
        
        // Make the mutate request.
        /** @var \ListReturnValue $returnValue */
        $returnValue = $service->mutate($operations);

        if(!$returnValue->value) {
            return null;
        }

        foreach($returnValue->value as $result) {
            $this->trigger('after', $operator, $result);
        }

        return $entitiesCount == 1 ? $returnValue->value[0] : $returnValue->value;
    }

    /**
     *  Get results by predicate
     *
     * @param AdWordsUser $adWordsUser
     * @param mixed       $predicate
     * @param array       $fields
     * @param \OrderBy|null       $orderBy
     * @param string|null       $serviceName
     *
     * @return array|mixed an array of results
     */
    public function get(AdWordsUser $adWordsUser, Array $fields, $predicate, $orderBy = null, $serviceName = null)
    {
        $service = $this->getService($adWordsUser, $serviceName);

        if(is_numeric($predicate)) {
            if(!property_exists($this->getEntityClassName(), 'id')) {
                throw new \InvalidArgumentException(
                    'Id is not selectable in '.$this->getEntityClassName().'.'
                );
            }

            $predicate = new \Predicate("Id", "EQUALS", $predicate);
        }

        // Create paging controls.
        $offset = 0;
        $entries = [];

        do {
            $selector = new \Selector($fields, $predicate);

            if($orderBy) {
                $selector->ordering = $orderBy;
            }


            $selector->paging = new \Paging($offset, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

            /** @var \Page $page */
            $page = $service->get($selector);

            // Display results.
            if (isset($page->entries)) {
                foreach ($page->entries as $entry) {
                    $entries[] = $entry;
                }
            }

            // Advance the paging offset.
            $offset += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
        } while ($page->totalNumEntries > $offset);

        return $entries;
    }

    /**
     * Get first result by predicate
     *
     * @param AdWordsUser $adWordsUser
     * @param array       $fields
     * @param             $predicate
     *
     * @return mixed|null the first result or null
     */
    public function first(AdWordsUser $adWordsUser, Array $fields, $predicate)
    {
        $results = $this->get($adWordsUser, $fields, $predicate);
        return empty($results) ? null : $results[0];
    }

    /**
     * @param $adWordsUser
     *
     * @return \BatchJobService|\LaravelGoogleAds\Services\AdWordsService
     */
    public function getService($adWordsUser, $serviceName = false)
    {
        return $this->getUserService($adWordsUser, $serviceName ?: $this->getServiceName());
    }

    /**
     * @param Operation $operation
     * @param           $operand
     * @param string    $operator
     *
     * @return Operation
     */
    protected function fillOperation(Operation $operation, $operand, $operator = 'ADD')
    {
        $operation->operator = $operator;
        $operation->operand = $operand;
        
        return $operation;
    }

    /**
     * @param $position
     * @param $operator
     * @param $entity
     *
     * @return mixed
     */
    protected function trigger($position, $operator, $entity)
    {
        return $this->fireEvent($this->getEventName($position, $operator), [$entity ]);
    }

    /**
     * @param $position
     * @param $operator
     *
     * @return string
     */
    protected function getEventName($position, $operator)
    {
        return  $this->getEventNamespace().".".
                $this->getServiceName().".".
                $this->eventNamesByOperator[$operator][$position];
    }

    /**
     * @return string
     */
    protected function getOperationClassName()
    {
        return $this->getEntityClassName().'Operation';
    }

    /**
     * @return string
     */
    protected function getServiceName()
    {
        return $this->getEntityClassName().'Service';
    }

    /**
     * The AdGroupDataHandler is used across repositories to
     * prepare a containing adgroup of a given entity
     *
     * @return AdgroupDataHandler
     */
    protected function getAdGroupDataHandler() : AdgroupDataHandler
    {
        return app('idmkr.adwords.adgroup.handler.data');
    }
}