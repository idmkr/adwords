<?php namespace Idmkr\Adwords\Handlers\Feed;

use Feed;

interface FeedDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  mixed  $data
	 * @return Feed
	 */
	public function prepare($data);
}
