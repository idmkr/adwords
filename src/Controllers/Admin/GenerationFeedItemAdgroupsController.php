<?php namespace Idmkr\Adwords\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Idmkr\Adwords\Repositories\GenerationFeedItemAdgroup\GenerationFeedItemAdgroupRepositoryInterface;

class GenerationFeedItemAdgroupsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Adwords repository.
	 *
	 * @var \Idmkr\Adwords\Repositories\GenerationFeedItemAdgroup\GenerationFeedItemAdgroupRepositoryInterface
	 */
	protected $generationfeeditemadgroups;

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
	 * @param  \Idmkr\Adwords\Repositories\GenerationFeedItemAdgroup\GenerationFeedItemAdgroupRepositoryInterface  $generationfeeditemadgroups
	 * @return void
	 */
	public function __construct(GenerationFeedItemAdgroupRepositoryInterface $generationfeeditemadgroups)
	{
		parent::__construct();

		$this->generationfeeditemadgroups = $generationfeeditemadgroups;
	}

	/**
	 * Display a listing of generationfeeditemadgroup.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::generationfeeditemadgroups.index');
	}

	/**
	 * Datasource for the generationfeeditemadgroup Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->generationfeeditemadgroups->grid();

		$columns = [
			'id',
			'feed_item_id',
			'generation_id',
			'adwords_adgroup_id',
			'adwords_adgroup_status',
			'adwords_adgroup_ads_count',
			'adwords_adgroup_keywords_count',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.idmkr.adwords.generationfeeditemadgroups.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new generationfeeditemadgroup.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new generationfeeditemadgroup.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating generationfeeditemadgroup.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating generationfeeditemadgroup.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified generationfeeditemadgroup.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->generationfeeditemadgroups->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("idmkr/adwords::generationfeeditemadgroups/message.{$type}.delete")
		);

		return redirect()->route('admin.idmkr.adwords.generationfeeditemadgroups.all');
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
				$this->generationfeeditemadgroups->{$action}($row);
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
		// Do we have a generationfeeditemadgroup identifier?
		if (isset($id))
		{
			if ( ! $generationfeeditemadgroup = $this->generationfeeditemadgroups->find($id))
			{
				$this->alerts->error(trans('idmkr/adwords::generationfeeditemadgroups/message.not_found', compact('id')));

				return redirect()->route('admin.idmkr.adwords.generationfeeditemadgroups.all');
			}
		}
		else
		{
			$generationfeeditemadgroup = $this->generationfeeditemadgroups->createModel();
		}

		// Show the page
		return view('idmkr/adwords::generationfeeditemadgroups.form', compact('mode', 'generationfeeditemadgroup'));
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
		// Store the generationfeeditemadgroup
		list($messages) = $this->generationfeeditemadgroups->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("idmkr/adwords::generationfeeditemadgroups/message.success.{$mode}"));

			return redirect()->route('admin.idmkr.adwords.generationfeeditemadgroups.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
