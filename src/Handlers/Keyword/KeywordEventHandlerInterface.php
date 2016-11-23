<?php namespace Idmkr\Adwords\Handlers\Keyword;

use Idmkr\Adwords\Models\Keyword;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface KeywordEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a keyword is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a keyword is created.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @return mixed
	 */
	public function created(Keyword $keyword);

	/**
	 * When a keyword is being updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Keyword $keyword, array $data);

	/**
	 * When a keyword is updated.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @return mixed
	 */
	public function updated(Keyword $keyword);

	/**
	 * When a keyword is being deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @return mixed
	 */
	public function deleting(Keyword $keyword);

	/**
	 * When a keyword is deleted.
	 *
	 * @param  \Idmkr\Adwords\Models\Keyword  $keyword
	 * @return mixed
	 */
	public function deleted(Keyword $keyword);

}
