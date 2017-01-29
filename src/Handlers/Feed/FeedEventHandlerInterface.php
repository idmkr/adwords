<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface FeedEventHandlerInterface extends BaseEventHandlerInterface
{
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
	 * @param  \AdCustomizerFeed  $feed
	 * @return mixed
	 */
	public function created(AdCustomizerFeed $feed);

	/**
	 * When a feed is being updated.
	 *
	 * @param  \AdCustomizerFeed  $feed
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(AdCustomizerFeed $feed, array $data);

	/**
	 * When a feed is updated.
	 *
	 * @param  \AdCustomizerFeed  $feed
	 * @return mixed
	 */
	public function updated(AdCustomizerFeed $feed);

	/**
	 * When a feed is being deleted.
	 *
	 * @param  \AdCustomizerFeed  $feed
	 * @return mixed
	 */
	public function deleting(AdCustomizerFeed $feed);

	/**
	 * When a feed is deleted.
	 *
	 * @param  \AdCustomizerFeed  $feed
	 * @return mixed
	 */
	public function deleted(AdCustomizerFeed $feed);

}
