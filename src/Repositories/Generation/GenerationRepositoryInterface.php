<?php namespace Idmkr\Adwords\Repositories\Generation;

interface GenerationRepositoryInterface{

	/**
	 * Returns a dataset compatible with data grid.
	 *
	 * @return \Idmkr\Adwords\Models\Generation
	 */
	public function grid();

	/**
	 * Returns all the adwords entries.
	 *
	 * @return \Idmkr\Adwords\Models\Generation
	 */
	public function findAll();

	/**
	 * Returns a adwords entry by its primary key.
	 *
	 * @param  int  $id
	 * @return \Idmkr\Adwords\Models\Generation
	 */
	public function find($id);

	/**
	 * Determines if the given adwords is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determines if the given adwords is valid for update.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates or updates the given adwords.
	 *
	 * @param  int  $id
	 * @param  array  $input
	 * @return bool|array
	 */
	public function store($id, array $input);

	/**
	 * Creates a adwords entry with the given data.
	 *
	 * @param  array  $data
	 * @return \Idmkr\Adwords\Models\Generation
	 */
	public function create(array $data);

	/**
	 * Updates the adwords entry with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Idmkr\Adwords\Models\Generation
	 */
	public function update($id, array $data);

	/**
	 * Deletes the adwords entry.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

}
