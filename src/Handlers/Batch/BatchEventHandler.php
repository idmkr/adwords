<?php namespace Idmkr\Adwords\Handlers\Batch;

use BatchJob;
use Exception;
use Idmkr\Adwords\Models\Generation;
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class BatchEventHandler extends BaseEventHandler implements BatchEventHandlerInterface
{
    /**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('idmkr.adwords.batch.upload.success', __CLASS__.'@uploadSuccess');
		$dispatcher->listen('idmkr.adwords.batch.upload.abort', __CLASS__.'@uploadAbort');
		$dispatcher->listen('idmkr.adwords.batch.upload.fail', __CLASS__.'@uploadFail');
		$dispatcher->listen('idmkr.adwords.batch.download.polling', __CLASS__.'@downloadPolling');
		$dispatcher->listen('idmkr.adwords.batch.download.success', __CLASS__.'@downloadSuccess');
	}

    public function uploadAbort($generationData)
    {

    }

    public function uploadSuccess($generationData)
    {

    }

    public function uploadFail($generationData)
    {
    }

    public function downloadPolling($generationData)
    {
    }

    public function downloadSuccess($generationData)
    {

    }

    public function downloadFail($generationData)
    {

    }

}
