<?php

namespace Devio\Eavquent\Attribute;

use Devio\Eavquent\Events\AttributeWasSaved;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * Model timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'label', 'model', 'entity', 'default_value', 'collection',
    ];

    /**
     * Attribute constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(eav_table('attributes'));

        parent::__construct($attributes);
    }

    /**
     * Registering events.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(AttributeWasSaved::class . '@handle');
    }

    /**
     * Get the attribute name name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Get the model class name.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->getAttribute('model');
    }

    /**
     * Return the model class.
     *
     * @return mixed
     */
    public function getModelInstance()
    {
        $class = $this->getAttribute('model');

        return new $class;
    }

    /**
     * Check if attribute is multivalued.
     *
     * @return bool
     */
    public function isCollection()
    {
        return (bool) $this->getAttribute('collection');
    }
}
