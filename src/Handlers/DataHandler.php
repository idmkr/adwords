<?php namespace Idmkr\Adwords\Handlers;

use Exception;
use Idmkr\Adwords\Operations\Commits\Commit;
use Operation;

abstract class DataHandler
{
    /**
     * @var int The money unit factor to real value. Used across classes
     */
    protected $microAmountFactor = 1000000;

    /**
     * Transforms any data to AdWords Class
     *
     * @param $data
     *
     * @return mixed
     * @throws Exception
     */
    public function prepare($data)
    {
        if(method_exists($this, 'prepareArray') && is_array($data)) {
            return $this->prepareArray($data);
        }
        else if(method_exists($this, 'prepareString') && is_string($data)) {
            return $this->prepareString($data);
        }
        else if(method_exists($this, 'prepareInt') && is_int($data)) {
            return $this->prepareInt($data);
        }
        else if($data instanceof \Operation) {
            return $data->operand;
        }
        else if($data instanceof Commit) {
            return $data->operation->operand;
        }
        else if(!is_object($data)) {
            throw new Exception(static::class." can't parse '$data'(".gettype($data)."), prepare".ucfirst(gettype($data))." method needed.");
        }
        else return $data;
    }

    public function equals($source, $target, $diffProperties = ["id"])
    {
        $diffProperties = $this->mapDiffProperties($diffProperties);

        foreach ($diffProperties as $property) {
            $valueSource = $this->getItemProperty($source, $property);
            $valueTarget = $this->getItemProperty($target, $property);

            if ($valueSource !== $valueTarget) {
                return false;
            }
        }
        return true;
    }

    public function getItemProperty($item, $property, $default = '')
    {
        if(is_callable($property)) {
            return $property($item);
        }
        else if(isset($item->{$property})) {
            return $item->{$property};
        }

        return $default;
    }

    /**
     * Validates any data requirements and throw adequate error.
     *
     * @param $data
     * @param $requiredKeys
     *
     * @throws Exception
     */
    protected function requireData($data, $requiredKeys)
    {
        foreach($requiredKeys as $key) {
            if(!isset($data[$key]) || empty($data[$key])) {
                throw new Exception(get_class($this)." prepare method require '$key' key to be defined and not empty.");
            }
        }
    }

    private function mapDiffProperties($diffProperties)
    {
        if(method_exists($this, 'getPropertiesMap')) {
            $diffPropertiesMap =  $this->getPropertiesMap();
        }
        else {
            $diffPropertiesMap = [];
        }

        $returnedProperties = [];

        foreach($diffProperties as $k => $property) {
            if(isset($diffPropertiesMap[$property])) {
                $returnedProperties[$property] = $diffPropertiesMap[$property];
            }
            else {
                $returnedProperties[$property] = $property;
            }
        }

        return $returnedProperties;
    }
}