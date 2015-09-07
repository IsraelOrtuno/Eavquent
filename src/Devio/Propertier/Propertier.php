<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Relations\HasManyProperties;

abstract class Propertier extends Model
{
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

    public function isProperty($key)
    {
        // $key will be property when it does not belong to any relationship
        // name and it also exists into the entity properties collection.
        // This way it won't interfiere with the model base behaviour.
        return $this->getRelationValue($key)
            ? false
            : $this->getPropertiesKeyed()->has($key);
    }

    /**
     * Will return the properties collection keyed by name.
     * This way filtering will be much easier.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getPropertiesKeyed($key = 'name')
    {
        return $this->getRelationValue('properties')->keyBy($key);
    }
}