<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;

class Writer
{
    /**
     * Entity instance.
     *
     * @var Model
     */
    protected $entity;

    /**
     * Property instance.
     *
     * @var Property
     */
    protected $property;

    /**
     * Writer constructor.
     *
     * @param Model $entity
     * @param Property $property
     */
    public function __construct(Model $entity, Property $property)
    {
        $this->entity = $entity;
        $this->property = $property;
    }

    /**
     * Create a Writer instance.
     *
     * @param Model $entity
     * @param Property $property
     * @return static
     */
    public static function make(Model $entity, Property $property)
    {
        return new static($entity, $property);
    }

    /**
     * Assign a value to a property.
     *
     * @param $value
     * @return PropertyValue|mixed|void
     */
    public function set($value)
    {
        if ($this->property->isMultivalue()) {
            return $this->assignMany($value);
        }

        return $this->assignSingle($value);
    }

    /**
     * Asign a value to the property value model.
     *
     * @param $value
     * @return PropertyValue
     */
    protected function assignSingle($value)
    {
        if ($propertyValue = $this->property->values) {
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
     * Will assign multiple values to the same property. Any previous values
     * stored will be queued for deletion and replaced for the new ones.
     *
     * @param Property $property
     * @param          $valueCollection
     *
     * @throws PropertyIsNotMultivalue
     */
    protected function assignMany(Property $property, $valueCollection)
    {
        if (! $valueCollection instanceof Collection || ! is_array($valueCollection)) {
            $valueCollection = new Collection($valueCollection);
        }

        // Any existing value will be added to the value deletion queue that
        // will be processed after saving. Meanwhile, the new values will
        // be created as new and added to the current values relation.
        $this->clearAndQueuePropertyValues($property);

        foreach ($valueCollection as $value) {
            $this->createNewValue($property, $value);
        }
    }

    /**
     * Will clear the property values relation and queue every value
     * for deletion in case the value is finally saved.
     *
     * @param Property $property
     */
    protected function clearAndQueuePropertyValues(Property $property)
    {
        $currentValues = $this->getValues($property);
        $this->queueForDeletion($currentValues);

        // Once the current property values are queued to be deleted, we have
        // to remove them from the property as they were already loaded in
        // the property relation. Just replace with an empty collection.
        $property->load([
            'values' => function () {
                return new Collection();
            }
        ]);
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
        $newValue = Value::createValue($this->property, $this->entity, $value);

        // After creating a new property value, we have to include it manually
        // into the property values relation collection. The "push" method
        // inlcuded in the collection will help us to perform this task.
        $this->property->setOrPushValue($newValue);

        return $newValue;
    }

    /**
     * Add items to the deletion queue.
     *
     * @param $valueCollection
     */
    protected function queueForDeletion($valueCollection)
    {
        $valueCollection->each(function ($value) {
            $this->entity->queueValueForDeletion($value);
        });
    }

    /**
     * Provides the property value model as collection or single element.
     *
     * @param      $property
     * @param bool $single
     *
     * @return mixed
     */
    protected function getValues($property, $single = false)
    {
        $values = $property->values;

        return ! $single ? $values : $values->first();
    }
}
