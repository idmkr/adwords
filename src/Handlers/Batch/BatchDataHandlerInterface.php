<?php namespace Idmkr\Adwords\Handlers\Batch;

use BatchJob;

interface BatchDataHandlerInterface
{
    public function prepare($data) : BatchJob;

}
