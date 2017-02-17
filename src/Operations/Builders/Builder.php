<?php namespace Idmkr\Adwords\Operations\Builders;


use Idmkr\Adwords\Operations\Commits\Commit;
use Idmkr\Adwords\Operations\Directors\DirectorInterface;
use Operation;

abstract class Builder
{
    /**
     * @var DirectorInterface
     */
    protected $director;

    protected $logLevel = 0;

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @param int $logLevel
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;
    }

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

    /**
     * @param \Operation $operation
     *
     * @return Commit
     */
    protected function commit(Operation $operation)
    {
        $this->log("Committing ".class_basename($operation->operand).".");
        return new Commit($operation);
    }

    public function log($msg, $logLevel = 1)
    {
        if($this->logLevel >= $logLevel) {
            $this->director->log($msg);
        }
    }
}