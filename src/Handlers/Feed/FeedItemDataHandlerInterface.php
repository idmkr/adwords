<?php namespace Idmkr\Adwords\Handlers\Feed;

use FeedItem;

interface FeedItemDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  mixed  $data
	 * @return FeedItem
	 */
	public function prepare($data);
}
