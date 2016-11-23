<?php namespace Idmkr\Adwords\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Idmkr\Adwords\Repositories\Adgroup\AdgroupRepositoryInterface;

class AdgroupsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Adwords repository.
	 *
	 * @var \Idmkr\Adwords\Repositories\Adgroup\AdgroupRepositoryInterface
	 */
	protected $adgroups;

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
	 * @param  \Idmkr\Adwords\Repositories\Adgroup\AdgroupRepositoryInterface  $adgroups
	 * @return void
	 */
	public function __construct(AdgroupRepositoryInterface $adgroups)
	{
		parent::__construct();

		$this->adgroups = $adgroups;
	}

	/**
	 * Display a listing of adgroup.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::adgroups.index');
	}

	/**
	 * Datasource for the adgroup Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->adgroups->grid();

		$columns = [
			'*',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.idmkr.adwords.adgroups.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new adgroup.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new adgroup.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating adgroup.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating adgroup.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified adgroup.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->adgroups->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("idmkr/adwords::adgroups/message.{$type}.delete")
		);

		return redirect()->route('admin.idmkr.adwords.adgroups.all');
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
				$this->adgroups->{$action}($row);
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
		// Do we have a adgroup identifier?
		if (isset($id))
		{
			if ( ! $adgroup = $this->adgroups->find($id))
			{
				$this->alerts->error(trans('idmkr/adwords::adgroups/message.not_found', compact('id')));

				return redirect()->route('admin.idmkr.adwords.adgroups.all');
			}
		}
		else
		{
			$adgroup = $this->adgroups->createModel();
		}

		// Show the page
		return view('idmkr/adwords::adgroups.form', compact('mode', 'adgroup'));
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
		// Store the adgroup
		list($messages) = $this->adgroups->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("idmkr/adwords::adgroups/message.success.{$mode}"));

			return redirect()->route('admin.idmkr.adwords.adgroups.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
