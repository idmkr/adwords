<?php namespace Idmkr\Adwords\Operations\Blueprints;


use Idmkr\Adwords\Operations\Directors\DirectorInterface;
use Idmkr\Adwords\Operations\Pipelines\BuildPipeline;

interface Blueprint
{
    /**
     * @param DirectorInterface $director
     *
     * @return BuildPipeline[]
     */
    public function execute($director) : Array;
}