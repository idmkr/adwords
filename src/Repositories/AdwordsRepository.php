<?php namespace Idmkr\Adwords\Repositories;

class AdwordsRepository
{
    public $version;

    public function __construct()
    {
        $this->version = config('google-ads.adWords.settings.SERVER.DEFAULT_VERSION');
    }

    public function requireService($file, $versioned = true)
    {
        $v = (!$versioned ? '' : $this->version."/");
        require_once(base_path("vendor/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/$v$file.php"));
    }

    /**
     * @param $object
     * @param $keys
     */
    protected function adwordsObjectToArray($object, $keys)
    {
        $data = [];
        $objData = get_object_vars($object);
        foreach($keys as $key => $dotKey) {
            $data[$key] = array_get($objData, $dotKey);
        }
        return $data;
    }
}