<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use FeedItem;
use FeedItemAttributeValue;
use Idmkr\Adwords\Handlers\DataHandler;
use Illuminate\Support\Collection;

class FeedItemDataHandler extends DataHandler implements FeedItemDataHandlerInterface
{

    private $feed;

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
		$attributeValues = (new Collection($data))->values()->map(function ($attr, $i){
			// Create the FeedItemAttributeValues for our text values.
			$attributeValue = new FeedItemAttributeValue();
			$attributeValue->feedAttributeId = $this->feed->feedAttributes[$i]->id;
			$attributeValue->stringValue = $attr;

			return $attributeValue;
		})->toArray();

		// Create the feed item and operation.
		$item = new FeedItem();
		$item->feedId = $this->feed->feedId;
		$item->attributeValues = $attributeValues;

		return $item;
	}
}
