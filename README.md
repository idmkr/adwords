This Laravel package provides an abstraction layer over AdWords PHP API. 
It relies on [nikolajlovenhardt/laravel-google-ads](https://github.com/nikolajlovenhardt/laravel-google-ads) for the authenticating part.

# Laravel Setup

First get your [authentication](https://github.com/nikolajlovenhardt/laravel-google-ads) up and running

Add provider to config/app.php
`'providers' => [
    Idmkr\Adwords\Providers\AdwordsServiceProvider::class,
],`