<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The entity the property is related to.
     *
     * @var Model
     */
    protected $entity = null;

    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = ['type', 'name', 'multivalue', 'entity', 'default_value'];

    /**
     * The property values relationship.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->hasMany(Value::class);
    }

    /**
     * Replicates a model and set it as existing.
     *
     * @return mixed
     */
    public function replicateExisting()
    {
        $instance = parent::replicate(['*']);
        $instance->exists = $this->exists;

        return $instance;
    }

    /**
     * Get the property value as single value or collection of values.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($values = $this->values)) {
            return $values;
        }

        // Will return null if there is no value for the property. If the property
        // is registered as a multi values property, we will return a collection
        // of values, otherwise we can return the plain object content instead.
        return $this->isMultivalue()
            ? $values->pluck('value')
            : $values->getAttribute('value');
    }

    /**
     * Set the value to the given value.
     *
     * @param Value $value
     * @return $this
     * @throws \Exception
     */
    public function setValue($value)
    {
        if (is_null($this->getEntity())) {
            throw new \Exception('No entity defined on property');
        }

        return ! $this->isMultivalue()
            ? $this->setSingleValue($value)
            : $this->setMultipleValue($value);
    }

    /**
     * Set or create a single value for the property.
     *
     * @param $value
     * @return Value
     */
    public function setSingleValue($value)
    {
        if ($propertyValue = $this->values) {
            $propertyValue->setValue($value);
        } else {
            $propertyValue = $this->createNewValue($value);
        }

        // We modify the value of the existing property value to the one passed
        // to the function. If there is no value related to the property, we
        // will create a new value instance and relate it to the property.
        return $propertyValue;
    }

    /**
     * Creates a new property value related to the given property
     * and the entity.
     *
     * @param $value
     * @return PropertyValue
     */
    protected function createNewValue($value)
    {
        // First we need to create a raw Value model instance and fill up all
        // its values. Once done, we've to transform it to a specific value
        // type passing the value model and the property to the resolver.
        $rawValue = Value::createValue($this, $this->getEntity(), $value);

        $newValue = (new Resolver)->value($this, $rawValue->getAttributes());

        // After creating a new property value, we have to include it manually
        // into the property values relation collection. The "push" method
        // inlcuded in the collection will help us to perform this task.
        $this->setOrPushValue($newValue);

        return $newValue;
    }

    /**
     * Pushes a new value into the values collection.
     *
     * @param Value $value
     * @return $this
     */
    public function pushValue(Value $value)
    {
        $this->initializeValues();

        $this->values->push($value);

        return $this;
    }

    /**
     * Pushes a new value into the values relationship collection.
     *
     * @param $value
     * @return mixed
     */
    public function setOrPushValue($value)
    {
        return $this->isMultivalue()
            ? $this->pushValue($value)
            : $this->setValue($value);
    }

    /**
     * Initialize the values relation to an empty collection.
     *
     * @return $this
     */
    protected function initializeValues()
    {
        if (is_null($this->values)) {
            $this->setRelation('values', new Collection);
        }

        return $this;
    }

    /**
     * Check if the property accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return $this->getAttribute('multivalue');
    }

    /**
     * Set the property entity.
     *
     * @param Model $entity
     * @return Property
     */
    public function entity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get the property entity.
     *
     * @return Model
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
