<?php namespace Idmkr\Adwords\Handlers\Operation;

use Idmkr\Adwords\Handlers\DataHandler;

class OperationDataHandler extends DataHandler
{
    /**
     * build an operation
     *
     * @param array $data the attributes
     */
    public function prepareModel($data) : Array
    {
        $operations = [];

        foreach($data as $operation) {
            $operations[] = $this->prepareArray($operation);
        }

        return $operations;
    }

    public function prepareArray($data)
    {
        $operation = new \Operation();

        $operation->OperationType = $data["operand_type"];
        $operation->operator = $data["operator"];
        //$operation->operand =
    }
}
