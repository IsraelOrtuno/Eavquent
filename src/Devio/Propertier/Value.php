<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Listeners\SavingValues;
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
     * The parent entity.
     *
     * @var Propertier
     */
    protected $parentEntity = null;

    /**
     * Booting the model.
     */
    public static function boot()
    {
        parent::boot();

        // Setting up the model event listeners. Much more elegant would be if
        // placed into the Service Provider. As this class is considered as
        // abstract, we have to set up the listeners at children classes.
        static::saving(SavingValues::class . '@handle');
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
     * @param array $attributes
     * @param $exists
     * @return static
     */
    public static function createInstance(array $attributes, $exists)
    {
        with($instance = new static)->setRawAttributes($attributes);
        $instance->exists = $exists;

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
    public static function createValue($property, $entity, $value)
    {
        with($instance = new static)->setAttribute('value', $value);

        // We could use the relation associate() method here instead of setting
        // every value separately. We are going this way as associate() sets
        // the entity relation and causes infinite loop when using push().
        $instance->setAttribute($instance->entity()->getForeignKey(), $entity->getKey());
        $instance->setAttribute($instance->entity()->getMorphType(), $entity->getMorphClass());
        $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());

        // In order to avoid the issue mentioned above, we will set the instances
        // of property and entity as parents of this value model. This will let
        // us access these objects in future without setting them as relation.
        $instance->setParents($property, $entity);

        return $instance;
    }

    /**
     * Set the value parents.
     *
     * @param $property
     * @param $entity
     */
    public function setParents($property, $entity)
    {
        $this->parentProperty = $property;
        $this->parentEntity = $entity;
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
     * Get the parent property.
     *
     * @return Property
     */
    public function getParentProperty()
    {
        return $this->parentProperty;
    }

    /**
     * Get the parent entity.
     *
     * @return Propertier
     */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }
}
