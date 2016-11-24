<?php namespace Idmkr\Adwords\Directors;

use Idmkr\Adwords\Operations\Builders\Builder;

interface DirectorInterface
{
    public function get();
    public function build(Builder $builder);
    public function getData() : array;
    public function getAdGroupTemplate() : array;
}