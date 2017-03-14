<?php namespace Idmkr\Adwords\Collections;

use Idmkr\Adwords\Handlers\MutateResult\MutateResultDataHandler;
use Idmkr\Adwords\Operations\Commits\Commit;
use Illuminate\Support\Collection;
use stdClass;


/**
 * Class AdCollection
 *
 * @package Idmkr\Adwords\Collections
 */
class MutateResultCollection extends AdwordsCollection
{
    protected $dataHandler = MutateResultDataHandler::class;

    /**
     * @param int $resultIndex
     *
     * @return mixed|null
     */
    public function getResult(int $resultIndex)
    {
        $mutateResult = $this->get($resultIndex);
        return $this->extractMutedObject($mutateResult);
    }

    /**
     * @param int $resultIndex
     *
     * @return mixed|null
     */
    public function getResultErrors(int $resultIndex) : Collection
    {
        $mutateResult = $this->get($resultIndex);
        return $this->extractErrors($mutateResult);
    }

    /**
     * Extract all results indexed by operation number
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getResults($type = null) : Collection
    {
        $objects = [];
        $this->each(function (\MutateResult $mutateResult) use (&$objects, $type) {
            if ($mutateResult->result) {
                $object = $this->extractMutedObject($mutateResult);
                if(($type && $object instanceof $type) || !$type) {
                    $objects[$mutateResult->index] = $object;
                }
            }
        });
        return new Collection($objects);
    }

    /**
     * Extract all errors indexed by operation number
     *
     * @param $mutateResults
     *
     * @return array
     */
    public function getErrors() : Collection
    {
        $errors = [];
        $this->each(function (\MutateResult $mutateResult) use (&$errors) {
            if ($mutateResult->errorList) {
                $errors[$mutateResult->index] = $this->extractErrors($mutateResult);
            }
        });
        return new Collection($errors);
    }

    /**
     * @param \MutateResult $mutateResult
     *
     * @return Collection|null
     */
    public function extractErrors(\MutateResult $mutateResult) : Collection
    {
        return new Collection( $mutateResult->errorList ? $mutateResult->errorList->errors : [] );
    }

    /**
     * @param array $errorList
     *
     * @return Collection
     */
    public function groupErrors(Array $errorList) : Collection
    {
        return (new Collection($errorList))
            ->map(function ($error) {
                return [
                    "errorString" => $error->errorString,
                    "trigger" => $error->trigger,
                    "fieldPath" => $error->fieldPath
                ];
            })->groupBy('errorString')->map(function (Collection $errorsByType) {
                $errorsByType = $errorsByType->map(function ($error) {
                    return collect($error)->forget('errorString');
                });

                $max = 50;
                if($errorsByType->count() > $max) {
                    $errorsByType = $errorsByType->slice(0, $max)->push([
                        "..." => ($errorsByType->count() - $max)." others errors of the same type were returned."
                    ]);
                }

                return $errorsByType;
            })->toArray();
    }

    /**
     * @param $mutateResult
     *
     * @return mixed
     */
    private function extractMutedObject($mutateResult)
    {
        return (new Collection($mutateResult->result))->first(function ($entityName, $entity) {
            return $entity;
        });
    }

    public function hasPolicyViolationErrors()
    {
        return !empty($this->getPolicyViolationErrors([]));
    }

    /**
     * @param array $operations
     * @param bool  $break
     *
     * @return array
     */
    public function getPolicyViolationErrors(Array $operations, $wantedOperationType = null)
    {
        $policyErrors = [];
        foreach($this->getErrors() as $operationIndex => $errors) {
            $trademarkPayload = null;
            /** @var \PolicyViolationError $error */
            foreach($errors as $error) {
                // Now check for PolicyViolationError
                if ($error instanceof \PolicyViolationError) {
                    // Instantiate here as we test the null value
                    if(!$trademarkPayload) {
                        $trademarkPayload = ["errors" => []];
                    }

                    if(!empty($operations) && isset($operations[$operationIndex])) {
                        $operation = $operations[$operationIndex];
                        if(is_a($operation, Commit::class)) {
                            $operation = $operation->operation;
                        }
                        $operand = $operation->operand;
                        if($operand instanceof stdClass) {
                            $operationType = $operation->OperationType;
                        }
                        else {
                            $operationType = class_basename($operand);
                        }

                        if(!$wantedOperationType || $operationType == $wantedOperationType) {
                            if ($operationType == 'AdGroupAd') {
                                $trademarkPayload['AdGroupAd'] = $operand->ad;
                            }
                            else if ($operationType = 'BiddableAdGroupCriterion') {
                                $trademarkPayload['BiddableAdGroupCriterion'] = $operand->criterion;
                            }
                            else {
                                throw new \Exception('Unhandled error type : '.class_basename($operand));
                            }
                        }
                        // We do not want this error
                        else {
                            break;
                        }
                    }

                    $fieldPaths = explode('.', $error->fieldPath);
                    $trademarkPayload["errors"][] = [
                        "field" => array_pop($fieldPaths),
                        "text" => $error->key->violatingText,
                        "type" => $error->key->policyName
                    ];
                }
            }

            // Some fields contains policy errors
            if ($trademarkPayload) {
                $policyErrors[$operationIndex] = $trademarkPayload;
            }
        }
        return $policyErrors;
    }

    /**
     *
     * @return MutateResultCollection
     */
    public function havingPolicyViolationErrors()
    {
        $policyErrors = [];
        foreach($this->getErrors() as $operationIndex => $errors) {
            /** @var \PolicyViolationError $error */
            foreach($errors as $error) {
                // Now check for PolicyViolationError
                if ($error instanceof \PolicyViolationError) {
                    $policyErrors[$operationIndex] = $this->get($operationIndex);
                }
            }
        }
        return new static($policyErrors);
    }
}