<?php namespace Idmkr\Adwords\Operations\Builders;

use Idmkr\Adwords\Directors\DirectorInterface;
use Illuminate\Support\Collection;

abstract class Builder
{
    /**
     * @var DirectorInterface
     */
    protected $director;

    /**
     * @param $scope
     *
     * @return mixed
     */
    abstract public function build($scope);

    /**
     * @param DirectorInterface $director
     */
    public function setDirector(DirectorInterface $director)
    {
        $this->director = $director;
    }

    /**
     * @param Collection|null      $iteratedCollection
     * @param callable|null        $callableMap
     * @param Collection|null $returnedCollection
     *
     * @return Collection
     */
    protected function dataMapByAdGroup(
        $iteratedCollection,
        callable $callableMap = null,
        Collection $returnedCollection = null
    ){
        if(!$returnedCollection) {
            $returnedCollection = new Collection();
        }

        foreach($this->getData() as $data) {
            $key = $this->generateAdGroupName($data);
            if($iteratedCollection) {
                $returnedCollection[$key] = $returnedCollection->make();

                foreach($iteratedCollection as $entity) {
                    $returnedCollection[$key]->push($callableMap($data, $entity));
                }
            }
            else if($callableMap) {
                $returnedCollection[$key] = $callableMap($data);
            }
            else {
                $returnedCollection[$key] = $data;
            }
        }

        return $returnedCollection;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function renameVarsToAdwordsVars($data, $feedName)
    {
        foreach ($data as &$value) {
            $value = preg_replace(
                '/\{(.+?)\}/',
                '{=' .$feedName .'.$1}',
                $value
            );
        }

        return $data;
    }

    /**
     * @param      $data
     * @param      $feedItem
     * @param null $varTransformCallback
     *
     * @return mixed
     */
    protected function applyFeedVars($data, $feedItem, $varTransformCallback = null)
    {
        $vars = [];
        foreach ($feedItem as $field => $v) {
            $vars['{' . $field . '}'] = $v;
        }

        if(is_callable($varTransformCallback)) {
            foreach($vars as $varName => &$varValue) {
                $varValue = $varTransformCallback($varName, $varValue);
            }
        }

        if (is_array($data)) {
            foreach ($data as &$value) {
                $value = strtr($value, $vars);
            }
            return $data;
        } else {
            return strtr($data, $vars);
        }
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->director->getData();
    }

    /**
     * @return array
     */
    protected function getAdGroupTemplate()
    {
        return $this->director->getAdGroupTemplate();
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function generateAdGroupName($data)
    {
        return $this->applyFeedVars($this->getAdGroupTemplate()["name"], $data);
    }
}