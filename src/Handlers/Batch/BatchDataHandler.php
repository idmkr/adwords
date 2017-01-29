<?php namespace Idmkr\Adwords\Handlers\Batch;

use BatchJob;
use Idmkr\Adwords\Handlers\DataHandler;
use Idmkr\Adwords\Traits\RequireAdWordsServiceTrait;

class BatchDataHandler extends DataHandler implements BatchDataHandlerInterface
{
    use RequireAdWordsServiceTrait;
    
    public function __construct()
    {
        $this->requireService("Util/".$this->getAdwordsApiVersion()."/BatchJobUtils", false);
    }

    public function prepare($data) : BatchJob
	{
		return new BatchJob();
	}

}
