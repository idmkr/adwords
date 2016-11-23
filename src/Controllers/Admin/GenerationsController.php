<?php namespace Idmkr\Adwords\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Idmkr\Adwords\Repositories\Generation\GenerationRepositoryInterface;

class GenerationsController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Adwords repository.
	 *
	 * @var \Idmkr\Adwords\Repositories\Generation\GenerationRepositoryInterface
	 */
	protected $generations;

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
	 * @param  \Idmkr\Adwords\Repositories\Generation\GenerationRepositoryInterface  $generations
	 * @return void
	 */
	public function __construct(GenerationRepositoryInterface $generations)
	{
		parent::__construct();

		$this->generations = $generations;
	}

	/**
	 * Display a listing of generation.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::generations.index');
	}

	/**
	 * Datasource for the generation Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->generations->grid();

		$columns = [
			'id',
			'adwords_batch_job_id',
			'templategroupeannonce_id',
			'feed_id',
			'adwords_feed_id',
			'operations_count',
			'status',
			'ended_at',
			'adgroups_count',
			'spare_ads_count',
			'customized_ads_count',
			'keywords_count',
			'feed_updates_count',
			'enabled',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.idmkr.adwords.generations.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new generation.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new generation.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating generation.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating generation.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified generation.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->generations->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("idmkr/adwords::generations/message.{$type}.delete")
		);

		return redirect()->route('admin.idmkr.adwords.generations.all');
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
				$this->generations->{$action}($row);
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
		// Do we have a generation identifier?
		if (isset($id))
		{
			if ( ! $generation = $this->generations->find($id))
			{
				$this->alerts->error(trans('idmkr/adwords::generations/message.not_found', compact('id')));

				return redirect()->route('admin.idmkr.adwords.generations.all');
			}
		}
		else
		{
			$generation = $this->generations->createModel();
		}

		// Show the page
		return view('idmkr/adwords::generations.form', compact('mode', 'generation'));
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
		// Store the generation
		list($messages) = $this->generations->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("idmkr/adwords::generations/message.success.{$mode}"));

			return redirect()->route('admin.idmkr.adwords.generations.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
