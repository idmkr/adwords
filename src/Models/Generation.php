<?php namespace Idmkr\Adwords\Models;

use Cartalyst\Attributes\EntityInterface;
use Idmkr\Template\Models\Templategroupeannonces;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Platform\Attributes\Traits\EntityTrait;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Generation extends Model implements EntityInterface {

	use EntityTrait, NamespacedEntityTrait, SoftDeletes;

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'generations';

	/**
	 * {@inheritDoc}
	 */
	protected $guarded = [
		'id',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $with = [
		'values.attribute',
	];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'errors' => 'array',
    ];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at','ended_at'];

	/**
	 * {@inheritDoc}
	 */
	protected static $entityNamespace = 'idmkr/adwords.generation';

    public function adGroupTemplate()
    {
        return $this->belongsTo(Templategroupeannonces::class, 'templategroupeannonce_id');
    }

    public function templategroupeannonce()
    {
        return $this->belongsTo(Templategroupeannonces::class, 'templategroupeannonce_id');
    }

	public function updates()
	{
		return $this->hasMany(self::class);
	}

	public function feed()
	{
		return $this->belongsTo('Idmkr\Feeds\Models\Feed');
	}
}
