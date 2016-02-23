<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Listeners\ValueSaved;
use Devio\Propertier\Listeners\ValueSaving;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    /**
     * Property Value fillable attributes.
     *
     * @var array
     */
    protected $fillable = ['value', 'entity_type', 'partner_id', 'field_id'];

    /**
     * The table every value will use.
     *
     * @var string
     */
    protected $table = 'field_values';

    /**
     * The parent property.
     *
     * @var Property
     */
    protected $parentProperty = null;

    /**
     * The factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Booting the model.
     */
    public static function boot()
    {
        parent::boot();

        // Setting up the model event listeners. Much more elegant would be if
        // placed into the Service Provider. As this class is considered as
        // abstract, we have to set up the listeners at children classes.
//        static::saving(ValueSaving::class . '@handle');
    }

    /**
     * Relationship to the fields table.
     *
     * @return BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
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
     * Replicates a value and set it as existing.
     *
     * @return Model
     */
    public function replicateExisting()
    {
        $instance = parent::replicate(['*']);
        $instance->exists = $this->exists;

        $instance->syncOriginal();
        $instance->setProperty($this->getProperty());

        return $instance;
    }

    /**
     * Make a new casted value instance.
     *
     * @param Property $property
     * @param $attributes
     * @return mixed
     */
    public static function make(Property $property, $attributes = [])
    {
        if (! is_array($attributes)) {
            $attributes = ['value' => $attributes];
        }

        with($instance = new static)->fill($attributes);

        return $instance->castObjectTo($property);
    }

    /**
     * Sync the relation attributes with parents.
     *
     * @return $this
     */
    public function syncRelationAttributes()
    {
        if (! is_null($entity = $this->getEntity())) {
            $this->entity()->associate($entity);
        }

        if (! is_null($property = $this->getProperty())) {
            $this->property()->associate($property);
        }

        $this->unsetRelations();

        return $this;
    }

    /**
     * Unset any existing relation.
     */
    public function unsetRelations()
    {
        $this->relations = [];
    }

    /**
     * Casting a raw value object to a value type.
     *
     * @param Property $property
     * @return self
     */
    public function castObjectTo(Property $property)
    {
        // Checking if this object is an instance of ::self would let us know
        // it this object has been already casted. A little bit tricky but
        // this will prevent errors if casting is called more than once.
        if ($this->isCasted()) {
            return $this;
        }

        $cast = $this->getFactory()->property($property);

        // Once we have guessed what's the value object we are casting to, lets
        // instantiate and make it look like a copy of the current model. It
        // will be an exact copy of the base model into a different class.
        with($cast = new $cast)->setRawAttributes($this->getAttributes());
        $cast->exists = $this->exists;

        // In case we are casting an existing value model, such as loaded from
        // a relation, we will also sync the original attributes. We need to
        // do this to let Eloquent know if value has changed before saving.
        if ($this->exists) {
            $cast->syncOriginal();
        }

        $cast->setProperty($property);

        return $cast;
    }

    /**
     * Check if the value is already casted.
     *
     * @return $this
     */
    protected function isCasted()
    {
        return get_class($this) != Value::class;
    }

    /**
     * Remove null values from database.
     *
     * @return mixed
     */
    public static function flush()
    {
        return static::where('value', null)->delete();
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
        if (is_null($property = $this->getProperty())) {
            return $property;
        }

        return $this->getProperty()->getEntity();
    }

    /**
     * Set the value factory.
     *
     * @param $factory
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the value factory or a new instance.
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory ?: new Factory;
    }
}
