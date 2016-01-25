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
     * The propertier query instance.
     *
     * @var PropertierQuery
     */
    protected $propertierQuery;

    // TODO: When saving, push() the properties of the model if any.
    // TODO: this will save only unsaved values :D

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

        if ($query->isProperty($key)) {
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

        if ($query->isProperty($key) && ! $query->isModelColumn($key)) {
            return $query->setValue($key, $value);
        }

        // If the property to set is registered and does not correspond to any
        // model column we are free to set its value. Otherwise we will let
        // go the default Eloquent behaviour and return its value if any.
        return parent::setAttribute($key, $value);
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
        $reflection = new ReflectionClass(PropertierQuery::class);

        // If the method we are trying to call is available in the manager class
        // we will prevent the default Model call to the Query Builder calling
        // this method in the Manager class passing this existing instance.
        if ($reflection->hasMethod($method)) {
            return call_user_func_array([$this->propertierQuery(), $method], $parameters);
        }

        return parent::__call($method, $parameters);
    }
}