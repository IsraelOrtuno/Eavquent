<?php

namespace Devio\Propertier;

use Devio\Propertier\Exceptions\UnresolvedPropertyException;
use RuntimeException;

class PropertyResolver
{
    /**
     * All of the registered properties.
     *
     * @var array
     */
    protected static $properties = [];

    /**
     * Register the properties.
     *
     * @param $properties
     */
    public static function register($properties)
    {
        static::$properties = $properties;
    }

    /**
     * Get the right property type class based on the property provided.
     *
     * @param       $property
     * @param array $attributes
     * @return PropertyValue
     * @throws UnresolvedPropertyException
     */
    public function property($property, $attributes = [])
    {
        $class = $this->getClassName($property);

        if (! class_exists($class)) {
            throw new RuntimeException("Property class {$class} not found");
        }

        // Will create a new PropertyValue model based on the property passed as
        // argument. It will also fill the model attributes if they have been
        // provided and relate it to the property, eager loading included.
        $propertyValue = new $class($attributes);

        return $propertyValue;
    }

    /**
     * Resolves the property classpath.
     *
     * @param $property
     * @return PropertyAbstract
     * @throws UnresolvedPropertyException
     */
    protected function getClassName($property)
    {
        $type = $this->getPropertyType($property);

        if (is_null($type) || ! isset(static::$properties[$type])) {
            throw new RuntimeException('Error getting an unregisterd property type');
        }

        return static::$properties[$type];
    }

    /**
     * Get the type of a property.
     *
     * @param $property
     * @return mixed
     * @throws UnresolvedPropertyException
     */
    protected function getPropertyType($property)
    {
        if (is_string($property)) {
            return $property;
        } elseif ($property instanceof Property) {
            return $property->getAttribute('type');
        }

        return null;
    }
}
