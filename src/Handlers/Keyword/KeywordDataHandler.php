<?php namespace Idmkr\Adwords\Handlers\Keyword;

use Idmkr\Adwords\Handlers\DataHandler;
use Keyword;

class KeywordDataHandler extends DataHandler 
{

	/**
	 * build an Keyword
	 *
	 * @param array $data the attributes
	 */
	public function prepareString(string $text) : Keyword
	{
		$keyword = new Keyword();

		if(starts_with($text, '"')) {
			$keyword->matchType = 'PHRASE';
			$text = str_replace('"', '', $text);
		}
		else if(starts_with($text, '[')) {
			$keyword->matchType = 'EXACT';
			$text = preg_replace('/[\[\]]/', '', $text);
		}
		else {
			$keyword->matchType = 'BROAD';
		}

		$keyword->text = $text;

		return $keyword;
	}

	/**
	 * build an Keyword
	 *
	 * @param array $data the attributes
	 */
	public function prepareArray(array $data) : Array
	{
		$keywords = [];

		foreach($data as $keyword) {
			$keywords[] = $this->prepareString($keyword);
		}

		return $keywords;
	}
}
