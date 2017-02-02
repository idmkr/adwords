<?php namespace Idmkr\Adwords\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Idmkr\Adwords\Repositories\Feed\FeedRepositoryInterface;

class FeedsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Adwords repository.
	 *
	 * @var \Idmkr\Adwords\Repositories\Feed\FeedRepositoryInterface
	 */
	protected $feeds;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
		'enable',
		'disable',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Idmkr\Adwords\Repositories\Feed\FeedRepositoryInterface  $feeds
	 * @return void
	 */
	public function __construct(FeedRepositoryInterface $feeds)
	{
		parent::__construct();

		$this->feeds = $feeds;
	}

	/**
	 * Display a listing of feed.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::feeds.index');
	}

	/**
	 * Datasource for the feed Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->feeds->grid();

		$columns = [
			'*',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.idmkr.adwords.feeds.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new feed.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new feed.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating feed.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating feed.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified feed.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->feeds->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("idmkr/adwords::feeds/message.{$type}.delete")
		);

		return redirect()->route('admin.idmkr.adwords.feeds.all');
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = request()->input('action');

		if (in_array($action, $this->actions))
		{
			foreach (request()->input('rows', []) as $row)
			{
				$this->feeds->{$action}($row);
			}

			return response('Success');
		}

		return response('Failed', 500);
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		// Do we have a feed identifier?
		if (isset($id))
		{
			if ( ! $feed = $this->feeds->find($id))
			{
				$this->alerts->error(trans('idmkr/adwords::feeds/message.not_found', compact('id')));

				return redirect()->route('admin.idmkr.adwords.feeds.all');
			}
		}
		else
		{
			$feed = $this->feeds->createModel();
		}

		// Show the page
		return view('idmkr/adwords::feeds.form', compact('mode', 'feed'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Store the feed
		list($messages) = $this->feeds->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("idmkr/adwords::feeds/message.success.{$mode}"));

			return redirect()->route('admin.idmkr.adwords.feeds.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
