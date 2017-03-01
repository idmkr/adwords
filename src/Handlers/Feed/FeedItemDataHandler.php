<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use AdCustomizerFeedAttribute;
use FeedAttribute;
use FeedItem;
use FeedItemAttributeValue;
use Idmkr\Adwords\Handlers\DataHandler;
use Illuminate\Support\Collection;
use Money;
use MoneyWithCurrency;

class FeedItemDataHandler extends DataHandler implements FeedItemDataHandlerInterface
{
    /** @var AdCustomizerFeed */
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
        $attributesValues = [];
        $feedAttributesById = $this->getFeedAttributesById();

        foreach(array_values($data) as $i => $value){
            // The data index will serve as the ID, because we know
            // The same logic was used to create the feed
            $feedAttribute = $feedAttributesById[$i+1];

            // Create the FeedItemAttributeValues for our text values.
            $attributeValue = new FeedItemAttributeValue();
            $attributeValue->feedAttributeId = $feedAttribute->id;

            $value = str_replace(',','.',trim($value));
            $type = $feedAttribute->type;

            // is it a price ? Is it not a zero equivalent value ?
            if($type == 'PRICE' && floatval($value)) {
                // Money Type is not saved, i dont know why
                // IF USED, DONT FORGET TO MAP THE PROPERTY FOR DIFF ( see AdGroupDataHandler )
                /*$money = new Money();
                $money->microAmount = $value * $this->microAmountFactor;

                $moneyWithCurrencyValue = new MoneyWithCurrency();
                $moneyWithCurrencyValue->money = $money;
                $moneyWithCurrencyValue->currencyCode = 'EUR';

                $attributeValue->moneyWithCurrencyValue = $moneyWithCurrencyValue;*/

                if(strpos($value, '€') === false) {
                    $value = trim($value).' €';
                }

                $attributeValue->stringValue = $value;
            }
            // is it a whole number ? Is it not a zero equivalent value ?
            else if($type == 'INTEGER' && intval($value)) {
                $attributeValue->integerValue = intval($value);
            }
            // its a string. Is it not a zero equivalent value ?
            else if(!preg_match("/^0*\s?[,.]?\s?0*\s?[%€]?$/", $value)) {
                $attributeValue->stringValue = $value;
            }

            // The attribute will be empty if the value was zero equivalent.
            // This way the feed item will not be used for the ad
            $attributesValues[] = $attributeValue;
        }
        return $attributesValues;
	}

	/**
	 * @param mixed $feedItem
	 *
	 * @return array
	 */
	public function getItemAttributes($feedItem) : array
	{
		$attrNames = $this->getFeedAttributesById()->map(function (AdCustomizerFeedAttribute $feedAttribute) {
			return $feedAttribute->name;
		});
		$attrValues = collect($feedItem->attributeValues)->keyBy(function (FeedItemAttributeValue $feedItemAttributeValue) {
			return $feedItemAttributeValue->feedAttributeId;
		})->map(function (FeedItemAttributeValue $feedItemAttributeValue) {
			return $feedItemAttributeValue->stringValue ?: $feedItemAttributeValue->integerValue;
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

    public function attributeTypeIsNumber($attr)
    {
        return !preg_match('/[%€]/', $attr) && is_numeric($attr);
    }

    /**
     * @return AdCustomizerFeedAttribute[]
     */
    private function getFeedAttributesById()
    {
        return collect($this->feed->feedAttributes)->keyBy(function (AdCustomizerFeedAttribute $feedAttribute) {
            return $feedAttribute->id;
        });
    }
}
