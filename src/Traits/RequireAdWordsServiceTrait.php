<?php namespace Idmkr\Adwords\Traits;

use LaravelGoogleAds\AdWords\AdWordsUser;
use LaravelGoogleAds\Services\AdWordsService;

trait RequireAdWordsServiceTrait
{
    /**
     * @var string
     */
    private $adwordsApiVersion;

    /**
     * @param      $file
     * @param bool $versioned
     */
    protected function requireService($file, $versioned = true)
    {
        $v = (!$versioned ? '' : $this->getAdwordsApiVersion()."/");
        require_once(base_path("vendor/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/$v$file.php"));
    }

    /**
     * @param AdWordsUser $user
     */
    protected function getUserService(AdWordsUser $user, string $serviceName)
    {
        return $user->GetService($serviceName, $this->getAdwordsApiVersion());
    }

    /**
     * @return null
     */
    protected function getAdwordsApiVersion()
    {
        return $this->setAdwordsApiVersion();
    }

    /**
     * @param null $version
     *
     * @return null
     */
    protected function setAdwordsApiVersion($version = null)
    {
        if($version) {
            $this->adwordsApiVersion = $version;
        }
        else if(!$this->adwordsApiVersion) {
            $this->adwordsApiVersion = config('google-ads.adWords.settings.SERVER.DEFAULT_VERSION');
        }

        return $this->adwordsApiVersion;
    }
}