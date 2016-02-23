<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class Field extends Model
{
    /**
     * The entity the property is related to.
     *
     * @var Model
     */
    protected $entity = null;

    /**
     * Original property value.
     *
     * @var null
     */
    protected $originalValue = null;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = ['type', 'name', 'multivalue', 'partner', 'default_value'];

    /**
     * Property constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->queue = new Queue;
//        $this->resetValueRelation();
    }

    /**
     * Replicates a property and set it as existing.
     *
     * @return mixed
     */
    public function replicateExisting()
    {
        $instance = parent::replicate(['*']);
        $instance->exists = $this->exists;

        $instance->syncOriginal();
        $instance->syncOriginalValue();

        return $instance->entity($this->getEntity());
    }

    /**
     * Load a collection of values.
     *
     * @param Collection $values
     * @return $this
     */
    public function loadValue(Collection $values)
    {
        // If the property already contains a values relationship, we do not
        // want to interfiere, this will be a breaking error. If not will
        // initialize the relation with the values that belong to it.
        $values = $this->extractValue($values);

        $values = $this->cast($values);

        // If the property is multivalue, we will set the values to the "values"
        // relation. Otherwise we will pick the first value of the collection
        // and set it to the "value" relation as it accepts a single value.
        if ($values instanceof Collection && ! $this->isMultivalue()) {
            $values = $values->first();
        }

        $this->setValueRelation($values);

        // We also have to sync the original values. This will let us control
        // which values may have changed when we try to save the entity. It
        // will also help us to roll back to the original property state.
        return $this->syncOriginalValue();
    }

    /**
     * Cast either a value or a collection.
     *
     * @param $values
     * @return mixed
     */
//    public function cast($values)
//    {
//        if (! $values instanceof BaseCollection) {
//            return $values->castObjectTo($this);
//        }
//
//        return $values->map(function ($value) {
//            return $value->castObjectTo($this);
//        });
//    }

    /**
     * Setting the values to the relation.
     *
     * @param Collection $values
     * @return $this
     */
//    public function setValueRelation($values)
//    {
//        return $this->setRelation($this->getValueRelationName(), $values);
//    }

    /**
     * Get the raw value relationship. It could be null, a Collection or
     * a single value Object.
     *
     * @return mixed
     */
//    public function getValueRelation()
//    {
//        return $this->getRelationValue($this->getValueRelationName());
//    }

    /**
     * Get the name of the value relation based on the property type.
     *
     * @return string
     */
//    public function getValueRelationName()
//    {
//        return $this->isMultivalue() ? 'values' : 'value';
//    }

    /**
     * Will reset the values relation (many).
     */
//    public function resetValueRelation()
//    {
//        $value = $this->isMultivalue() ? new Collection : null;
//
//        $this->setRelation($this->getValueRelationName(), $value);
//    }

    /**
     * Extract only the values of this property.
     *
     * @param Collection $values
     * @return static
     */
//    protected function extractValue(Collection $values)
//    {
//        return $values->filter(function ($item) {
//            return $item->getAttribute($this->getForeignKey()) == $this->getKey();
//        });
//    }

    /**
     * Get the property value as single value or collection of values.
     *
     * @return mixed
     */
//    public function get()
//    {
//        if (is_null($value = $this->getValueRelation())) {
//            return $value;
//        }
//
//        // A collection of values means this property is multivalue. As of it
//        // we'll map the collection in to trigger the value casting or its
//        // mutators. Will return a set of plain values in a collection.
//        if ($value instanceof Collection) {
//            return $value->map(function ($item) {
//                return $item->getAttribute('value');
//            });
//        }
//
//        return $value->getAttribute('value');
//    }

    /**
     * Get the raw value object.
     *
     * @return mixed
     */
//    public function getObject()
//    {
//        return $this->getValueRelation();
//    }
//
//    /**
//     * Set the value to the given value.
//     *
//     * @param Value $value
//     * @return $this
//     */
//    public function set($value)
//    {
//        if (! $this->isMultivalue()) {
//            return $this->setOne($value);
//        }
//
//        if (! is_array($value) && ! $value instanceof Collection) {
//            $value = func_get_args();
//        }
//
//        return $this->setMany($value);
//    }

    /**
     * Set a single value.
     *
     * @param $value
     * @return Property
     */
//    protected function setOne($value)
//    {
//        if (! is_null($valueItem = $this->getValueRelation())) {
//            return $valueItem->setAttribute('value', $value);
//        }
//
//        return $this->setValueRelation(Value::make($this, $value));
//    }
//
//    /**
//     * Set values for multivalue.
//     *
//     * @param $values
//     * @return $this
//     */
//    protected function setMany($values)
//    {
//        $this->enqueueCurrentValue();
//
//        // Will add the current values relation to the deletion queue in order to
//        // be deleted if persisted. After that we will just have to push every
//        // item found in the values array to the new empty values relation.
//        $this->resetValueRelation();
//
//        foreach ($values as $value) {
//            $this->getValueRelation()->push(Value::make($this, $value));
//        }
//
//        return $this;
//    }
//
//    /**
//     * Add the current values to the deletion queue.
//     *
//     * @return mixed
//     */
//    public function enqueueCurrentValue()
//    {
//        if (! $values = $this->getValueRelation()) {
//            return $values;
//        }
//
//        if (! is_array($values) && ! $values instanceof Collection) {
//            $values = [$values];
//        }
//
//        $queue = $this->getQueue();
//
//        // We will only add the values that already exist in database as not
//        // persisted values do not need to be deleted. Every value will be
//        // added to the deletion queue to be processed if model is saved.
//        foreach ($values as $value) {
//            $queue->add($value);
//        }
//    }

    /**
     * Add to deletion queue.
     *
     * @param $value
     */
//    public function enqueueForDeletion($value)
//    {
//        $this->getQueue()->add($value);
//    }

    /**
     * Sync the original value relation.
     *
     * @return $this
     */
//    public function syncOriginalValue()
//    {
//        $this->originalValue = $this->getValueRelation();
//
//        return $this;
//    }

    /**
     * Check if the property accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return (bool)$this->getAttribute('multivalue');
    }

    /**
     * Set the property entity.
     *
     * @param Model $entity
     * @return Property
     */
//    public function entity($entity)
//    {
//        $this->entity = $entity;
//
//        return $this;
//    }

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
     * Get the original value.
     *
     * @return null
     */
    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    /**
     * Get the deletion queue.
     *
     * @return array
     */
    public function getQueue()
    {
        return $this->queue;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }
}
