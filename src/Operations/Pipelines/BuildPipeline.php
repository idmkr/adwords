<?php namespace Idmkr\Adwords\Operations\Pipelines;

use Idmkr\Adwords\Operations\Builders\Builder;
use Idmkr\Adwords\Operations\Commits\Commit;
use Idmkr\Adwords\Operations\Directors\DirectorInterface;
use Illuminate\Support\Collection;

class BuildPipeline
{
    private $payload;
    public $scope;
    /**
     * @var Commit[] $commits
     */
    protected $commits = [];
    /**
     * @var DirectorInterface
     */
    protected $director;

    /**
     * BuildPipeline constructor.
     *
     * @param $director
     * @param $scope
     */
    public function __construct($director, $scope)
    {
        $this->scope = $scope;
        $this->director = $director;
    }

    /**
     * @param $operator
     *
     * @return array
     */
    private function filterCommits($operator)
    {
        $filteredCommits = [];
        foreach ($this->commits as $commit) {
            if ($commit->operation->operator == $operator) {
                $filteredCommits[] = $commit;
            }
        }
        return $filteredCommits;
    }

    /**
     * Build stuff through builders
     *
     * @param Builder  $builder
     * @param callable $buildCallback
     *
     * @return self
     */
    public function through(Builder $builder)
    {
        $commits = $builder->setDirector($this->director)->build($this->scope, $this->payload);
        return $this->addCommits($commits);
    }

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function then(callable $callback)
    {
        $commits = $this->getCommits();
        foreach ($commits as $i => $commit) {
            $ret = $callback($commit, $this->getPayload(), $i);

            // If return is a builder, then send it through the current pipeline
            if(is_a($ret, Builder::class)) {
                $this->through($ret);
            }
        }
        return $this;
    }

    /**
     * @return Commit[]
     */
    public function getCommits($operator = null)
    {
        return $operator ? $this->filterCommits($operator) : $this->commits;
    }

    /**
     * @param $commits
     *
     * @return self
     * @throws \Exception
     */
    public function addCommits($commits)
    {
        if ($commits instanceof Collection) {
            $commits = $commits->all();
        }

        if (is_array($commits)) {
            foreach ($commits as $i => $commit) {
                if (!$commit instanceof Commit) {
                    $type = is_object($commit) ? class_basename($commit) : gettype($commit);
                    throw new \Exception("addCommits(): array argument at index $i is not a commit, " . $type . " instead.");
                }
                $this->commits[] = $commit;
            }
        }
        else if ($commits instanceof Commit) {
            $this->commits[] = $commits;
        }
        else if($commits) {
            $type = is_object($commits) ? class_basename($commits) : gettype($commits);
            throw new \Exception("addCommits(): argument is not a commit, " . $type . " instead.");
        }

        return $this;
    }

    /**
     * @param mixed $payload
     *
     * @return self
     */
    public function send($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}