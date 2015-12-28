<?php

namespace Devio\Propertier;

use Devio\Propertier\Relations\MorphManyValues;
use Devio\Propertier\Relations\HasManyProperties;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Propertier
{
    /**
     * Base model attributes.
     *
     * @var array
     */
    protected static $modelAttributes = [];

    /**
     * Relationship to the properties table.
     *
     * @return HasManyProperties
     */
    public function properties()
    {
        $instance = new Property;

        // We are using a self coded relation as there is no foreign key into
        // the properties table. The entity name will be used as a foreign
        // key to find the properties which belong to this entity item.
        return new HasManyProperties(
            $instance->newQuery(), $this, $this->getMorphClass()
        );
    }

    /**
     * Polimorphic relationship to the values table.
     *
     * @return MorphMany
     */
    public function values()
    {
        $instance = new Value;
        list($type, $id) = $this->getMorphs('entity', null, null);
        $table = $instance->getTable();

        return new MorphManyValues(
            $instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $this->getKeyName()
        );
    }

    /**
     * Will check if the key exists as registerd property.
     *
     * @param $key
     * @return bool
     */
    public function isProperty($key)
    {
        // Checking if the key corresponds to any comlumn in the main entity
        // table. If there is a match, means the key is an existing model
        // attribute which value will be always taken before property.
        if (in_array($key, $this->getModelAttributes())) {
            return false;
        }

        // $key will be property when it does not belong to any relationship
        // name and it also exists into the entity properties collection.
        // This way it won't interfiere with the base model behaviour.
        return $this->getRelationValue($key)
            ? false
            : ! is_null($this->getProperty($key));
    }

    /**
     * Find a property object by name.
     *
     * TODO: [PRE-RELEASE] Think about how performance could be improved here
     *
     * @param $key
     * @return mixed
     */
    public function getProperty($key)
    {
        $properties = $this->getPropertiesRelation();

        // We will key our collection by name, this way will be much easier for
        // filtering. Once keyed, just checking if the property has a key of
        // the name passed as argument will mean that a property exists.
        $keyedProperties = $properties->keyBy('name');

        return $keyedProperties->has($key)
            ? $keyedProperties->get($key)
            : null;
    }

    /**
     * Will get the values of a property.
     *
     * @param       $key  Property name
     * @return mixed
     */
    public function getPropertyValue($key)
    {
        $property = $this->getProperty($key);
        // We will first grab the property object which contains a collection of
        // values linked to it. It will work even when setting elements that
        // are no yet persisted as they will be set into the relationship.

        return $property->values;
    }

    public function getPropertiesRelation()
    {
        return $this->getRelationValue('properties');
    }

    /**
     * Get the model main attributes.
     *
     * @return array
     */
    public function getModelAttributes()
    {
        // If no attributes are listed in $modelAttributes property, we will
        // fetch them from database. This could result into a performance
        // issue so it should be set manually or when booting the model.
        if (empty(static::$modelAttributes)) {
            static::$modelAttributes = $this->fetchModelAttributes();
        }

        return static::$modelAttributes;
    }

    /**
     * Get the names of the model columns.
     *
     * @return mixed
     */
    public function fetchModelAttributes()
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    /**
     * Overriding magic method.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->isProperty($key)) {
            return $this->getPropertyValue($key);
        }

        return parent::__get($key);
    }
}
