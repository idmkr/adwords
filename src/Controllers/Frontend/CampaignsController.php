<?php namespace Idmkr\Adwords\Controllers\Frontend;

use Platform\Foundation\Controllers\Controller;

class CampaignsController extends Controller {

	/**
	 * Return the main view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::index');
	}

}
