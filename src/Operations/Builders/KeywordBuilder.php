<?php namespace Idmkr\Adwords\Operations\Builders\Keyword;

use Idmkr\Adwords\Operations\Builders\Builder;
use Idmkr\Adwords\Collections\AdGroupCollection;
use Idmkr\Adwords\Collections\KeywordCollection;
use Idmkr\Adwords\Repositories\Keyword\KeywordRepository;

class KeywordBuilder extends Builder
{
    private $keywordTemplates;

    public function __construct(array $keywordTemplates)
    {
        $this->keywordTemplates = $keywordTemplates;
    }

    /**
     * @param AdGroupCollection $adGroups
     *
     * @return array
     */
    public function build($adGroups)
    {
        /** @var KeywordRepository $adwordsKeywords */
        $adwordsKeywords = app('idmkr.adwords.keyword');

        return $adwordsKeywords->buildAdGroupOperations($adGroups, $this->getKeywords());
    }

    private function getKeywords() : KeywordCollection
    {
        $keywordsData = array_filter(explode("\n",str_replace("\r","",$this->keywordTemplates)));

        return $this->dataMapByAdGroup($keywordsData, function ($feedItem, $keyword) {
            return $this->applyFeedVars($keyword, $feedItem, function ($varName, $varValue) use($keyword) {
                $varValue = $this->formatKeywordVar($keyword, $varName, $varValue, '+');
                return $varValue;
            });
        }, new KeywordCollection);
    }

    private function formatKeywordVar($keyword, $varName, $varValue,  $startChar)
    {
        if(strlen($startChar) == 2) {
            $startChar = substr($startChar,0,1);
            $endChar = substr($startChar,1);
        }
        $varPosition = strpos($keyword, $varName);

        if($varPosition !== false && substr($keyword, $varPosition-1, 1) == $startChar) {
            $words = explode(" ", $varValue);
            foreach ($words as &$word) {
                if (!starts_with($word, $startChar) && (!isset($endChar) || !ends_with($word,$endChar))) {
                    $word = $startChar.$word.(isset($endChar)?$endChar:'');
                }
            }
            $varValue = ltrim(implode(' ', $words), $startChar);
            if(isset($endChar)) {
                $varValue = rtrim(implode(' ', $words), $endChar);
            }
        }
        return $varValue;
    }
}