<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use FeedItem;
use FeedItemAttributeValue;
use Idmkr\Adwords\Handlers\DataHandler;
use Illuminate\Support\Collection;

class FeedItemDataHandler extends DataHandler implements FeedItemDataHandlerInterface
{

    public $feed;

    public function __construct(AdCustomizerFeed $feed)
    {
        $this->feed = $feed;
    }

    /**
	 * build a FeedItem
	 *
	 * @param array $data the attributes
	 */
	public function prepareArray(array $data) : FeedItem
	{
		// Create the feed item and operation.
		$item = new FeedItem();
		$item->feedId = $this->feed->feedId;
		$item->attributeValues = $this->getAttributesValues($data);

		return $item;
	}

	public function getAttributesValues(array $data)
    {
        return (new Collection($data))->values()->map(function ($attr, $i){
            // Create the FeedItemAttributeValues for our text values.
            $attributeValue = new FeedItemAttributeValue();
            $attributeValue->feedAttributeId = $this->feed->feedAttributes[$i]->id;

            $attr = str_replace(',','.',trim($attr));

            // is it a number ?
            if(!preg_match('/[%€]/', $attr) && is_numeric($attr) && floatval($attr)) {
                $attributeValue->doubleValue = $attr;
            }
            else {
                // Is it a zero equivalent value ?
                if(preg_match("/^0*\s?[,.]?\s?0*\s?[%€]?$/", $attr)) {
                    $attr = 'NULL';
                }

                $attributeValue->stringValue = $attr;
            }


            return $attributeValue;
        })->toArray();
	}

	/**
	 * @param mixed $feedItem
	 *
	 * @return array
	 */
	public function getItemAttributes($feedItem) : array
	{
		$attrNames = collect($this->feed->feedAttributes)->keyBy(function (\AdCustomizerFeedAttribute $feedAttribute) {
			return $feedAttribute->id;
		})->map(function (\AdCustomizerFeedAttribute $feedAttribute) {
			return $feedAttribute->name;
		});
		$attrValues = collect($feedItem->attributeValues)->keyBy(function (\FeedItemAttributeValue $feedItemAttributeValue) {
			return $feedItemAttributeValue->feedAttributeId;
		})->map(function (\FeedItemAttributeValue $feedItemAttributeValue) {
			return $feedItemAttributeValue->stringValue ?: $feedItemAttributeValue->doubleValue;
		});

		$attrs = [];
		foreach($attrNames as $attrId => $attrName) {
			$attrs[$attrName] = $attrValues[$attrId] ?? null;
		}

		return $attrs;
	}

	public function getItemProperty($feedItem, $property, $default = '')
	{
		$attributes = $this->getItemAttributes($feedItem);

        // We don't check on strings because $property can't be anything
        // including existing global functions
		if(!is_string($property) && is_callable($property)) {
			return $property($attributes);
		}
		else if(isset($attributes[$property])) {
			return $attributes[$property];
		}

		return $default;
	}
}
