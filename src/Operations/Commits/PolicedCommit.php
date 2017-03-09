<?php namespace Idmkr\Adwords\Operations\Commits;

class PolicedCommit extends Commit
{
    protected $policyViolationRules = "";
    public $adGroupName;

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
        $this->adGroupName = $adGroup->name;
        return $this;
    }
}