<?php namespace Idmkr\Adwords\Collections;

use Keyword;


/**
 * Class KeywordCollection
 *
 * @package Idmkr\Keywordwords\Collections
 */
class KeywordCollection extends AdwordsCollection
{
    /**
     * build an Keyword
     *
     * @param array $data the attributes
     */
    public function parseStringItem(string $text) : Keyword
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
    public function parseArrayItem(string $data) : Keyword
    {
        $keywords = [];

        foreach($data as $keyword) {
            $keywords[] = $this->parseStringItem($keyword);
        }

        return $keywords;
    }
}