<?php namespace Idmkr\Adwords\Operations\Commits;

class Commit
{
    /**
     * @var \Operation
     */
    public $operation;

    public function __construct(\Operation $operation)
    {
        $this->operation = $operation;
    }
}