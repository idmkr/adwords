<?php namespace Idmkr\Adwords\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Idmkr\Adwords\Repositories\User\UserRepositoryInterface;

class UsersController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Adwords repository.
	 *
	 * @var \Idmkr\Adwords\Repositories\User\UserRepositoryInterface
	 */
	protected $users;

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
	 * @param  \Idmkr\Adwords\Repositories\User\UserRepositoryInterface  $users
	 * @return void
	 */
	public function __construct(UserRepositoryInterface $users)
	{
		parent::__construct();

		$this->users = $users;
	}

	/**
	 * Display a listing of user.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('idmkr/adwords::users.index');
	}

	/**
	 * Datasource for the user Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->users->grid();

		$columns = [
			'id',
			'client_manager_id',
			'user_id',
			'client_customer_id',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.idmkr.adwords.users.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new user.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new user.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating user.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->users->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("idmkr/adwords::users/message.{$type}.delete")
		);

		return redirect()->route('admin.idmkr.adwords.users.all');
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
				$this->users->{$action}($row);
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
		// Do we have a user identifier?
		if (isset($id))
		{
			if ( ! $user = $this->users->find($id))
			{
				$this->alerts->error(trans('idmkr/adwords::users/message.not_found', compact('id')));

				return redirect()->route('admin.idmkr.adwords.users.all');
			}
		}
		else
		{
			$user = $this->users->createModel();
		}

		// Show the page
		return view('idmkr/adwords::users.form', compact('mode', 'user'));
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
		// Store the user
		list($messages) = $this->users->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("idmkr/adwords::users/message.success.{$mode}"));

			return redirect()->route('admin.idmkr.adwords.users.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
