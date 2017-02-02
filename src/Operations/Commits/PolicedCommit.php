<?php namespace Idmkr\Adwords\Operations\Commits;

class PolicedCommit extends Commit
{
    protected $policyViolationRules = "";
    public $adGroup;

    public function addPolicyViolationRules($rules)
    {
        $this->policyViolationRules .= "\n$rules";
        return $this;
    }

    public function getPolicyViolationRules()
    {
        return $this->policyViolationRules;
    }

    public function setAdGroup(\AdGroup $adGroup)
    {
        $this->adGroup = $adGroup;
        return $this;
    }
}