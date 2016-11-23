<?php namespace Idmkr\Adwords\Repositories\Generation;

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class GenerationRepository implements GenerationRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Idmkr\Adwords\Handlers\Generation\GenerationDataHandlerInterface
	 */
	protected $data;

	/**
	 * The Eloquent adwords model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->setContainer($app);

		$this->setDispatcher($app['events']);

		$this->data = $app['idmkr.adwords.generation.handler.data'];

		$this->setValidator($app['idmkr.adwords.generation.validator']);

		$this->setModel(get_class($app['Idmkr\Adwords\Models\Generation']));
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this
			->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		/*return $this->container['cache']->rememberForever('idmkr.adwords.generation.all', function()
		{*/
			return $this->createModel()->get();
		//});
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->container['cache']->rememberForever('idmkr.adwords.generation.'.$id, function() use ($id)
		{
			return $this->createModel()->find($id);
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $input)
	{
		return $this->validator->on('create')->validate($input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $input)
	{
		return $this->validator->on('update')->validate($input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function store($id, array $input)
	{
		return ! $id ? $this->create($input) : $this->update($id, $input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $input)
	{
		// Create a new generation
		$generation = $this->createModel();

		// Fire the 'idmkr.adwords.generation.creating' event
		if ($this->fireEvent('idmkr.adwords.generation.creating', [ $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForCreation($data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Save the generation
			$generation->fill($data)->save();

			// Fire the 'idmkr.adwords.generation.created' event
			$this->fireEvent('idmkr.adwords.generation.created', [ $generation ]);
		}

		return $generation;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $input)
	{
		// Get the generation object
		$generation = $this->find($id);

		// Fire the 'idmkr.adwords.generation.updating' event
		if ($this->fireEvent('idmkr.adwords.generation.updating', [ $generation, $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForUpdate($generation, $data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the generation
			$generation->fill($data)->save();

			// Fire the 'idmkr.adwords.generation.updated' event
			$this->fireEvent('idmkr.adwords.generation.updated', [ $generation ]);
		}

		return $generation;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		// Check if the generation exists
		if ($generation = $this->find($id))
		{
			// Fire the 'idmkr.adwords.generation.deleting' event
			$this->fireEvent('idmkr.adwords.generation.deleting', [ $generation ]);

			// Delete the generation entry
			$generation->delete();

			// Fire the 'idmkr.adwords.generation.deleted' event
			$this->fireEvent('idmkr.adwords.generation.deleted', [ $generation ]);

			return true;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function enable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => true ]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function disable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => false ]);
	}

	public function findWhere(...$args)
	{
		return $this->where($args)->get();
	}

	public function where(...$args)
	{
		return call_user_func_array([$this->createModel(), "where"], $args)->with('feed');
	}

	public function getAdwordsClientCustomerId($generation)
	{
		return $generation->adGroupTemplate->client_customer_id;
	}

}
