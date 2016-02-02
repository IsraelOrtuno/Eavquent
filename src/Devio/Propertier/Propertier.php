<?php

namespace Devio\Propertier;

use Devio\Propertier\Listeners\EntitySaved;
use Devio\Propertier\Listeners\EntitySaving;
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
     * Booting the trait.
     */
    public static function bootPropertier()
    {
        static::saving(EntitySaving::class . '@handle');
        static::saved(EntitySaved::class . '@handle');
    }

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
        $this->addHidden('values');

        return $this->morphMany(Value::class, 'entity');
    }

    /**
     * Get if properties could be accessed.
     *
     * @return bool
     */
    public function isPropertiesRelationAccessible()
    {
        return $this->getPropertiesAutoloading() || $this->relationLoaded('properties');
    }

    /**
     * Get properties autoloading property.
     *
     * @return bool
     */
    public function getPropertiesAutoloading()
    {
        if (property_exists($this, 'propertiesAutoloading')) {
            return $this->propertiesAutoloading;
        }

        return true;
    }

    /**
     * Get the model columns.
     *
     * @return array
     */
    public static function getModelColumns()
    {
        return static::$modelColumns;
    }

    /**
     * Set the model columns.
     *
     * @param $columns
     * @return array
     */
    public static function setModelColumns($columns)
    {
        if (! is_array($columns)) {
            $columns = func_get_args();
        }

        static::$modelColumns = $columns;
    }

    /**
     * Get a new manager instance.
     *
     * @return Manager
     */
    public function propertierQuery()
    {
        return new PropertierQuery($this);
    }

    /**
     * Overriding Eloquent getAttribute method will first read a property.
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $query = $this->propertierQuery();

        if ($this->isPropertiesRelationAccessible() &&
            $query->isProperty($key)
        ) {
            return $query->getValue($key);
        }

        // If the property we are accesing corresponds to a any registered property
        // we will provide the value of this property if any. Otherwise, we will
        // access the parent Eloquent model and return its default behaviour.
        return parent::getAttribute($key);
    }

    /**
     * Overriding Eloquent setAttribute method will first set a property.
     *
     * @param $key
     * @param $value
     * @return Value
     * @throws \Exception
     */
    public function setAttribute($key, $value)
    {
        $query = $this->propertierQuery();

        if ($this->isPropertiesRelationAccessible() &&
            $query->isProperty($key) &&
            ! $query->isModelColumn($key)
        ) {
            return $query->setValue($key, $value);
        }

        // If the property to set is registered and does not correspond to any
        // model column we are free to set its value. Otherwise we will let
        // go the default Eloquent behaviour and return its value if any.
        return parent::setAttribute($key, $value);
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    public function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();

        // We will sum an array of properties to the array of attributes in order
        // to avoid replacing any attribute with a property as original model
        // attribute names should be considered first instead of property.
        if ($this->isPropertiesRelationAccessible()) {
            $attributes = $attributes + $this->propertiesToArray();
        }

        return $attributes;
    }

    /**
     * Convert the properties to an array.
     *
     * @return array
     */
    public function propertiesToArray()
    {
        $values = $this->propertierQuery()->getValues();

        return $this->getArrayableItems($values->toArray());
    }

    /**
     * Handling propertier method calls to the manager class.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $query = $this->propertierQuery();

        // If the method we are trying to call is available in the manager class
        // we will prevent the default Model call to the Query Builder calling
        // this method in the Manager class passing this existing instance.
        if ($this->isPropertiesRelationAccessible() &&
            in_array($method, get_class_methods($query))
        ) {
            return call_user_func_array([$query, $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }
}
