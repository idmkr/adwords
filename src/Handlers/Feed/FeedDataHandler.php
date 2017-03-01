<?php namespace Idmkr\Adwords\Handlers\Feed;

use AdCustomizerFeed;
use Feed;
use Idmkr\Adwords\Handlers\DataHandler;

class FeedDataHandler extends DataHandler implements FeedDataHandlerInterface
{
   public function prepare($data)
   {
	   return $data;
   }

}
