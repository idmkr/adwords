<?php namespace Idmkr\Adwords\Handlers\Feed;

use Idmkr\Adwords\Models\Feed;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface FeedEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a feed is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a feed is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Feed  $feed
	 * @return mixed
	 */
	public function created(Feed $feed);

	/**
	 * When a feed is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Feed  $feed
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Feed $feed, array $data);

	/**
	 * When a feed is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Feed  $feed
	 * @return mixed
	 */
	public function updated(Feed $feed);

	/**
	 * When a feed is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Feed  $feed
	 * @return mixed
	 */
	public function deleting(Feed $feed);

	/**
	 * When a feed is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Feed  $feed
	 * @return mixed
	 */
	public function deleted(Feed $feed);

}
