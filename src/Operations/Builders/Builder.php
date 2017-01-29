<?php namespace Idmkr\Adwords\Operations\Builders;


use Idmkr\Adwords\Operations\Commits\Commit;
use Idmkr\Adwords\Operations\Directors\DirectorInterface;

abstract class Builder
{
    /**
     * @var DirectorInterface
     */
    protected $director;

    /**
     *
     * @return \Operation[]
     */
    abstract public function build($scope, $data);

    /**
     * @param $director
     */
    public function setDirector($director)
    {
        $this->director = $director;
        return $this;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->director->getData();
    }

    protected function commit(\Operation $operation)
    {
        return new Commit($operation);
    }
}