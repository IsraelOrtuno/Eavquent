<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Devio\Propertier\Exceptions\EntityNotFoundException;
use Illuminate\Support\Collection as BaseCollection;

class Property extends Model
{
    /**
     * The entity the property is related to.
     *
     * @var Model
     */
    protected $entity = null;

    /**
     * Values deletion queue.
     *
     * @var array
     */
    protected $deletionQueue = [];

    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = ['type', 'name', 'multivalue', 'entity', 'default_value'];

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

    public function loadValues(Collection $values)
    {
        if ($this->relationLoaded('values')) {
            throw new \RuntimeException('Values relation is already loaded.');
        }

        $values = $this->cast($values);

        // If the property already contains a values relationship, we do not
        // want to interfiere, this will be a breaking error. If not will
        // initialize the relation with the values that belong to it.
        $values = $this->extractValues($values);

        // If the property is multivalue, we will set the values to the "values"
        // relation. Otherwise we will pick the first value of the collection
        // and set it to the "value" relation as it accepts a single value.
        if ($this->isMultivalue()) {
            return $this->setRelation('values', $values);
        } else {
            return $this->setRelation('value', $values->first());
        }
    }

    /**
     * Cast either a value or a collection.
     *
     * @param $values
     * @return mixed
     */
    public function cast($values)
    {
        if (! $values instanceof BaseCollection) {
            return $values->castObjectTo($this);
        }

        return $values->map(function ($value) {
            return $value->castObjectTo($this);
        });
    }

    /**
     * Extract only the values of this property.
     *
     * @param Collection $values
     * @return static
     */
    protected function extractValues(Collection $values)
    {
        return $values->filter(function($item) {
            return $item->{$this->getForeignKey()} == $this->getKey();
        });
    }

    /**
     * Get the property value as single value or collection of values.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($value = $this->getValueObject())) {
            return $value;
        }

        // A collection of values means this property is multivalue. As of it
        // we'll map the collection in to trigger the value casting or its
        // mutators. Will return a set of plain values in a collection.
        if ($value instanceof Collection) {
            return $value->map(function ($item) {
                return $item->getAttribute('value');
            });
        }

        return $value->getAttribute('value');
    }

    /**
     * Get the raw value relationship. It could be null, a Collection or
     * a single value Object.
     *
     * @return mixed
     */
    public function getValueObject()
    {
        $relation = $this->isMultivalue() ? 'values' : 'value';

        return $this->getRelationValue($relation);
    }

    /**
     * Set the value to the given value.
     *
     * @param Value $value
     * @return $this
     * @throws EntityNotFoundException
     */
    public function setValue($value)
    {
//        if (is_null($this->getEntity())) {
//            throw new EntityNotFoundException('No entity defined on property');
//        }
//
//        return ! $this->isMultivalue()
//            ? $this->setSingleValue($value)
//            : $this->setMultiValue($value);
    }

    /**
     * Set or create a single value for the property.
     *
     * @param $value
     * @return Value
     */
    public function setSingleValue($value)
    {
        if ($this->values instanceof self) {
            $this->values->setValue($value);
        } else {
            $propertyValue = $this->createNewValue($value);
        }

        // We modify the value of the existing property value to the one passed
        // to the function. If there is no value related to the property, we
        // will create a new value instance and relate it to the property.
        return $propertyValue;
    }

    /**
     * Set a multi value collection replacing previous values.
     *
     * @param $values
     */
    public function setMultiValue($values)
    {
        if (! $values instanceof Collection && ! is_array($values)) {
            $values = func_get_args();
        }

        // As it is a multivalue property, we will make sure we are getting
        // an array of values. Also we have to fill up the deletion queue
        // with any existing values that this property may have linked.
        $this->enqueueValuesDeletion();

        // Once we have enqueued for deletion all the existing values in the
        // property, we have to force a reset into the values relationship
        // collection in order to fill it up again with the new values.
        $this->initializeValues(true);

        foreach ($values as $value) {
            $this->createNewValue($value);
        }
    }

    /**
     * Add the current property values to the deletion queue.
     */
    protected function enqueueValuesDeletion()
    {
        $existing = $this->values->where('exists', true);

        // We will only add the values that already exist in database as not
        // persisted values do not need to be deleted. Every value will be
        // added to the deletionQueue to be processed if model is saved.
        foreach ($existing as $value) {
            array_push($this->deletionQueue, $value);
        }
    }

    /**
     * Creates a new property value related to the given property and the entity.
     *
     * @param $value
     * @return PropertyValue
     */
    protected function createNewValue($value)
    {
        // We will create a new Value instance already resolved to the property
        // it is related to using the value resolver from the abstract Value
        // class. We set a fully transformed value object to the relation.
        $newValue = Value::resolveValue($this, $this->getEntity(), $value);

        // After creating a new property value, we have to include it manually
        // into the property values relation collection. The "push" method
        // inlcuded in the collection will help us to perform this task.
        $this->setOrPushValue($newValue);

        return $newValue;
    }

    /**
     * Pushes a new value into the values relationship collection.
     *
     * @param $value
     * @return mixed
     */
    public function setOrPushValue($value)
    {
        if ($this->isMultivalue()) {
            return $this->pushValue($value);
        }

        return $this->setRelation('values', $value);
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
     * Initialize the values relation to an empty collection.
     *
     * @param bool $force
     * @return $this
     */
    protected function initializeValues($force = false)
    {
        if (is_null($this->values) || $force) {
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

    /**
     * Get the deletion queue.
     *
     * @return array
     */
    public function getDeletionQueue()
    {
        return $this->deletionQueue;
    }
}
