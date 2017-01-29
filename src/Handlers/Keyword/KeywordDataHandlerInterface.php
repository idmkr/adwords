<?php namespace Idmkr\Adwords\Handlers\Keyword;

use Keyword;

interface KeywordDataHandlerInterface {

	/**
	 * Prepares the given data for being stored.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function prepare(array $data) : Keyword;

}
