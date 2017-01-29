<?php namespace Idmkr\Adwords\Handlers;

use Exception;
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
            return $this->prepareOperation($data);
        }
        else if(!is_object($data)) {
            throw new Exception(static::class." can't parse '$data'(".gettype($data)."), prepare".ucfirst(gettype($data))." method needed.");
        }
        else return $data;
    }

    /**
     * @param Operation $operation
     *
     * @return array
     */
    protected function prepareOperation(Operation $operation)
    {
        return $operation->operand;
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
}