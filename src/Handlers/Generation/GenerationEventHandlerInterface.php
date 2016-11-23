<?php namespace Idmkr\Adwords\Handlers\Generation;

use Idmkr\Adwords\Models\Generation;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface GenerationEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a generation is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a generation is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @return mixed
	 */
	public function created(Generation $generation);

	/**
	 * When a generation is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Generation $generation, array $data);

	/**
	 * When a generation is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @return mixed
	 */
	public function updated(Generation $generation);

	/**
	 * When a generation is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @return mixed
	 */
	public function deleting(Generation $generation);

	/**
	 * When a generation is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Generation  $generation
	 * @return mixed
	 */
	public function deleted(Generation $generation);

}
