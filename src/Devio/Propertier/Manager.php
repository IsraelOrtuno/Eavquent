<?php

namespace Devio\Propertier;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Manager
{
    /**
     * The entity instance.
     *
     * @var Model
     */
    protected $entity;

    /**
     * Manager constructor.
     *
     * @param Model $entity
     */
    public function __construct(Model $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Will check if the key exists as registerd property.
     *
     * @param $key
     * @return bool
     */
    public function isProperty($key)
    {
        // Checking if the key corresponds to any attribute of the main entity or
        // any relationship. If there's a match we'll we cannot assume they may
        // ever be a property as them are part of the core so more important.
        if ($this->getEntity()->getRelationValue($key) ||
            in_array($key, $this->getModelColumns()) ||
            $key == $this->getEntity()->getKeyName()
        ) {
            return false;
        }

        // $key will be property when it does not belong to any relationship
        // name and it also exists into the entity properties collection.
        // This way it won't interfiere with the base model behaviour.
        return ! is_null($this->getProperty($key));
    }

    /**
     * Find a property object by name.
     *
     * @param $key
     * @return mixed
     */
    public function getProperty($key)
    {
        // We will assume our collection is keyed by name as it is supposed to
        // happen into the relationship process. If the property has the key
        // we are looking for, will return it meaning the property exists.
        return $this->getEntity()->properties->get($key, null);
    }

    /**
     * Will get the values of a property.
     *
     * @param  $key Property name
     * @return mixed
     */
    public function getValue($key)
    {
        if (is_null($property = $this->getProperty($key))) {
            return $property;
        }

        return $property->getValue();
    }

    /**
     * Get the property raw value object/s.
     *
     * @param $key
     * @return mixed
     */
    public function getValueObject($key)
    {
        $property = $this->getProperty($key);

        // We will first grab the property object which contains a collection of
        // values linked to it. It will work even when setting elements that
        // are no yet persisted as they will be set into the relationship.
        return $property->getValueObject();
    }

    /**
     * Setting a property or a regular eloquent attribute.
     *
     * @param $key
     * @param $value
     * @return Value
     * @throws Exception
     */
    public function setValue($key, $value)
    {
        if (is_null($property = $this->getProperty($key))) {
            throw new Exception("Setting a {$key} property that does not exist.");
        }

        return $property->entity($this->getEntity())->setValue($value);
    }

    /**
     * Get the base model attribute names.
     *
     * @return array
     */
    public function getModelColumns()
    {
        $class = get_class($this->getEntity());

        // We have to resolve the entity class name in order to access its static
        // property $modelColums. This property is stored in the model as will
        // be different from one model to another.
        if (empty($class::$modelColumns)) {
            $class::$modelColumns = $this->fetchModelColumns();
        }

        // If no attributes are listed into $modelColumns property, we will
        // fetch them from database. This could result into a performance
        // issue so it should be set manually or when booting the model.
        return $class::$modelColumns;
    }

    /**
     * Check if an attribute corresponds to a model column name.
     *
     * @param $attribute
     * @return bool
     */
    public function isModelColumn($attribute)
    {
        return in_array($attribute, $this->getModelColumns());
    }

    /**
     * Get the model column names.
     *
     * @return mixed
     */
    public function fetchModelColumns()
    {
        $entity = $this->getEntity();

        return $entity->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($entity->getTable());
    }

    /**
     * Get the manager entity.
     *
     * @return Model
     */
    public function getEntity()
    {
        return $this->entity;
    }
}