<?php namespace Idmkr\Adwords\Collections;

use Idmkr\Adwords\Handlers\Keyword\KeywordDataHandler;
use Keyword;


/**
 * Class KeywordCollection
 *
 * @package Idmkr\Keywordwords\Collections
 */
class KeywordCollection extends AdwordsCollection
{
    protected $dataHandler = KeywordDataHandler::class;

    protected function getKeywords()
    {
        $keywords = [];

        /** @var Keyword $keyword */
        foreach($this->all() as $keyword) {
            $keywords[] = $keyword->text;
        }

        return $keywords;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  mixed  $items
     * @return static
     */
    public function merge($items)
    {
        return new static(array_merge(
            $this->getKeywords(),
            $items instanceof static ? $items->getKeywords() : $this->getArrayableItems($items)
        ));
    }
}