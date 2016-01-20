<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Listeners\SavingValue;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    /**
     * Property Value fillable attributes.
     *
     * @var array
     */
    protected $fillable = ['value', 'entity_type', 'entity_id', 'property_id'];

    /**
     * The table every value will use.
     *
     * @var string
     */
    protected $table = 'property_values';

    /**
     * The parent property.
     *
     * @var Property
     */
    protected $parentProperty = null;

    /**
     * Booting the model.
     */
    public static function boot()
    {
        parent::boot();

        // Setting up the model event listeners. Much more elegant would be if
        // placed into the Service Provider. As this class is considered as
        // abstract, we have to set up the listeners at children classes.
        static::saving(SavingValue::class . '@handle');
    }

    /**
     * Relationship to the properties table.
     *
     * @return BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Polimorphic relation to the entity this value belongs to.
     *
     * @return MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Creates a new instance.
     *
     * @param $property
     * @param array $attributes
     * @param $exists
     * @return static
     */
    public static function createInstanceFrom($property, array $attributes, $exists)
    {
        with($instance = new static)->setRawAttributes($attributes);
        $instance->exists = $exists;

        // In order to avoid the infinite loop problem, we will set the property
        // instance as parent of this value model. This will help us access to
        // it in future without having to set it as a regular relationship.
        $instance->setProperty($property);

        return $instance;
    }

    /**
     * Create a new value instance related to property and entity.
     *
     * @param $property
     * @param $entity
     * @param $value
     * @return static
     */
    public static function resolveValue($property, $entity, $value)
    {
        with($instance = new static)->setAttribute('value', $value);

        $instance->entity()->associate($entity);
        $instance->property()->associate($property);

        return (new Resolver)->value($property, $instance->getAttributes());
    }

    /**
     * Set the model value.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
    }

    /**
     * Set the parent property.
     *
     * @param $property
     */
    public function setProperty($property)
    {
        $this->parentProperty = $property;
    }

    /**
     * Get the parent property.
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->parentProperty;
    }

    /**
     * Get the parent entity.
     *
     * @return Propertier
     */
    public function getEntity()
    {
        return $this->getProperty()->getEntity();
    }
}
