<?php namespace Idmkr\Adwords\Collections;

use Illuminate\Support\Collection;
use Operation;

abstract class AdwordsCollection extends Collection
{
    protected $microAmountFactor = 1000000;

    /**
     * Create a new collection.
     *
     * @param  mixed  $items
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct();
        $this->items = $this->parseItems($items);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->items[] = $this->parseItem($value);
        } else {
            $this->items[$key] = $this->parseItem($value);
        }
    }

    protected function parseItems($items)
    {
        $parsed = [];
        foreach($items as $item) {
            $parsed[] = $this->parseItem($item);
        }
        return $parsed;
    }

    protected function parseItem($item)
    {
        if(method_exists($this, 'parseArrayItem') && is_array($item)) {
            return $this->parseArrayItem($item);
        }
        else if(method_exists($this, 'parseStringItem') && is_string($item)) {
            return $this->parseStringItem($item);
        }
        else if(method_exists($this, 'parseIntItem') && is_int($item)) {
            return $this->parseIntItem($item);
        }
        else if($item instanceof \Operation) {
            return $this->parseOperationItem($item);
        }
        else return $item;
    }

    public function __set($property, $value)
    {
        if ($property == 'items') {
            $this->items = $this->parseItems($value);
        }
    }
    /**
     * @param Operation $operation
     *
     * @return array
     */
    protected function parseOperationItem(Operation $operation)
    {
        return $operation->operand;
    }


    /**
     * Diff collections by using the main collection key as a lookup table
     * Optionally set the properties to look into and the lookup table key
     *
     * @param Collection|array $elements
     * @param mixed            $diffProperties
     * @param string           $key
     *
     * @return $this
     */
    public function diffKey($elements, $diffProperties = ["id"], $key = null)
    {
        $items = $this->all();

        if(!is_array($elements) && !($elements instanceof \Traversable)) {
            return $this->clone($items);
        }
        if(is_array($elements)) {
            $elements = $this->clone($elements);
        }

        $diffProperties = $this->mapDiffProperties($diffProperties);

        if($key) {
            $elements = $elements->keyBy($key);
        }

        foreach($items as $i => $sourceItem) {
            $localKey = $this->getItemProperty($sourceItem, $key ?: $i, $key ?: $i);

            $foundIdentical = true;
            if (isset($elements[$localKey])) {
                $targetItem = $elements[$localKey];
                foreach ($diffProperties as $property) {
                    $valueSource = $this->getItemProperty($sourceItem, $property);
                    $valueTarget = $this->getItemProperty($targetItem, $property);

                    if ($valueSource !== $valueTarget) {
                        $foundIdentical = false;
                        break;
                    }
                }
            }
            else {
                $foundIdentical = false;
            }
            if($foundIdentical) {
                unset($items[$i]);
            }
        }

        return $this->clone($items);
    }

    protected function getItemProperty($item, $property, $default = '')
    {
        if(is_callable($property)) {
            return $property($item);
        }
        else if(isset($item->{$property})) {
            return $item->{$property};
        }

        return $default;
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

    public function keyBy($key)
    {
        if(is_string($key)) {
            $keyed = $this->clone();
            foreach($this->all() as $item) {
                $keyed[$item->$key] = $item;
            }
            return $keyed;
        }

        return parent::keyBy($key);
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed  $items
     * @return static
     */
    public function clone($items = [])
    {
        return new static($items);
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    public function pluck($value, $key = null)
    {
        $results = [];

        list($value, $key) = static::explodePluckParameters($value, $key);

        foreach ($this->items as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    protected function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

        return [$value, $key];
    }
}