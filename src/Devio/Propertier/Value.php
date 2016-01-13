<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
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

        $instance->setAttribute($instance->entity()->getForeignKey(), $entity->getKey());
        $instance->setAttribute($instance->entity()->getMorphType(), $entity->getMorphClass());

        $instance->setAttribute($instance->property()->getForeignKey(), $property->getKey());

        return $instance;
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
     * Casting to database string when setting.
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->setValue((string)$value);
    }

    /**
     * Casting from database string when getting.
     *
     * @param $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return $value;
    }
}
