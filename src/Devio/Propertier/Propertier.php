<?php

namespace Devio\Propertier;

use Devio\Propertier\Relations\HasManyProperties;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Propertier
{
    /**
     * Model schema columns.
     *
     * @var array
     */
    public static $modelColumns = [];

    /**
     * Relationship to the properties table.
     *
     * @return HasManyProperties
     */
    public function properties()
    {
        // We are using a self coded relation as there is no foreign key into
        // the properties table. The entity name will be used as a foreign
        // key to find the properties which belong to this entity item.
        return new HasManyProperties(
            (new Property)->newQuery(), $this, $this->getMorphClass()
        );
    }

    /**
     * Polimorphic relationship to the values table.
     *
     * @return MorphMany
     */
    public function values()
    {
        return $this->morphMany(Value::class, 'entity');
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
        if (in_array($key, $this->getModelColumns())) {
            return false;
        }
        // $key will be property when it does not belong to any relationship
        // name and it also exists into the entity properties collection.
        // This way it won't interfiere with the base model behaviour.
        return is_null($this->getRelationValue($key))
            ? ! is_null($this->getProperty($key))
            : false;
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
        return $this->properties->get($key, null);
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
            return null;
        }
        
        $values = $property->values;

        // Will return null if the property does not exist. If the property is
        // registerd as a multi value property, we will return a collection
        // of values, otherwise we can return the plain object instead.
        return $property->isMultivalue()
            ? $values->pluck('values')
            : $values->getAttribute('value');
    }

    /**
     * Get the property raw value object/s.
     *
     * @param $key
     * @return mixed
     */
    public function getRawValue($key)
    {
        $property = $this->getProperty($key);

        // We will first grab the property object which contains a collection of
        // values linked to it. It will work even when setting elements that
        // are no yet persisted as they will be set into the relationship.
        return $property->values;
    }

    /**
     * Get the base model attribute names.
     *
     * @return array
     */
    public function getModelColumns()
    {
        if (empty(static::$modelColumns)) {
            static::$modelColumns = $this->fetchModelColumns();
        }

        // If no attributes are listed into $modelColumns property, we will
        // fetch them from database. This could result into a performance
        // issue so it should be set manually or when booting the model.
        return static::$modelColumns;
    }

    /**
     * Get the model column names.
     *
     * @return mixed
     */
    public function fetchModelColumns()
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
            return $this->getValue($key);
        }

        // If the property we are accesing corresponds to a any registered property
        // we will provide the value of this property if any. Otherwise, we will
        // access the parent Eloquent model and return its default behaviour.
        return parent::__get($key);
    }
}
