<?php

namespace Devio\Propertier;

use ReflectionClass;
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
        $this->addHidden('values');

        return $this->morphMany(Value::class, 'entity');
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
    public function newManagerQuery()
    {
        return new Manager($this);
    }

    /**
     * Overriding property reading.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $manager = $this->newManagerQuery();

        if ($manager->isProperty($key)) {
            return $manager->getValue($key);
        }

        // If the property we are accesing corresponds to a any registered property
        // we will provide the value of this property if any. Otherwise, we will
        // access the parent Eloquent model and return its default behaviour.
        return parent::__get($key);
    }

    /**
     * Override property setting.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        $manager = $this->newManagerQuery();

        if ($manager->isProperty($key) && ! static::isModelColumn($key)) {
            return $manager->setValue($key, $value);
        }

        // If the property to set is registered and does not correspond to any
        // model column we are free to set its value. Otherwise we will let
        // go the default Eloquent behaviour and return its value if any.
        return parent::__set($key, $value);
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
        $reflection = new ReflectionClass(Manager::class);

        // If the method we are trying to call is available in the manager class
        // we will prevent the default Model call to the Query Builder calling
        // this method in the Manager class passing this existing instance.
        if ($reflection->hasMethod($method)) {
            return call_user_func_array([$this->newManagerQuery(), $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Handling dynamic method calls to the manager class.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $reflection = new ReflectionClass(Manager::class);

        // If the method we are trying to call is available in the manager class
        // we will prevent the default Model call to the Query Builder calling
        // this method in the Manager class providing a new entity instance.
        if ($reflection->hasMethod($method)) {
            $manager = (new static)->newManagerQuery();

            return call_user_func_array([$manager, $method], $parameters);
        }

        return parent::__callStatic($method, $parameters);
    }
}