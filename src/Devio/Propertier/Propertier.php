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
     * Find a property by its name.
     *
     * @param $name
     * @return mixed
     */
    public function getProperty($name)
    {
        $properties = $this->getPropertiesRelationValue();

        return (new PropertyFinder($properties))->find($name);
    }

    /**
     * Will find the PropertyValue raw model instance based on
     * the key passed as argument.
     *
     * @param $key
     * @return null
     */
    public function getPropertyValue($key)
    {
//        $this->attachValues();
        $reader = new Reader();

        // This will mix the properties and the values and will decide which values
        // belong to what property. It will work even when setting elements that
        // are not persisted as they will be available into the relationships.
        return $reader->properties($this->getPropertiesRelationValue())
            ->values($this->getRelationValue('values'))
            ->read($key);
    }

    public function getPropertiesRelationValue()
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
