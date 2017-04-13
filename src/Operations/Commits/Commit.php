<?php namespace Idmkr\Adwords\Operations\Commits;

class Commit
{
    /**
     * @var \Operation
     */
    public $operation;
    private $feedItemId;

    public function __construct(\Operation $operation)
    {
        $this->operation = $operation;
    }

    public function setOperator(string $operator)
    {
        $this->operation->operator = $operator;
        return $this;
    }

    public function setFeedItemId($feedItemId)
    {
        $this->feedItemId = $feedItemId;
        return $this;
    }

    public function getFeedItemId()
    {
        return $this->feedItemId;
    }
}