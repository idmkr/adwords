This Laravel package provides an abstraction layer over AdWords PHP API. 
It relies on [nikolajlovenhardt/laravel-google-ads](https://github.com/nikolajlovenhardt/laravel-google-ads) for the authenticating part.

# Laravel Setup

First get your [authentication](https://github.com/nikolajlovenhardt/laravel-google-ads) up and running

Add provider to config/app.php

`'providers' => [
    Idmkr\Adwords\Providers\AdwordsServiceProvider::class,
],`

Tests coverage will come soon.
For now, here is what you can do with it  :
- Upload complete batch job using laravel style repositories
- Harness the power of Laravel collections and use it on adwords entities
- Feed based campaign management

# Example

See the [Batch Upload Test Case](https://github.com/idmkr/adwords/blob/master/tests/BatchUploadTestCase.php)