<?php

namespace Devio\Propertier;

use RuntimeException;

class Factory
{
    /**
     * All of the value types.
     *
     * @var array
     */
    protected static $valueTypes = [];

    /**
     * Register the properties.
     *
     * @param $valueTypes
     */
    public static function register($valueTypes)
    {
        static::$valueTypes = $valueTypes;
    }

    /**
     * Guess the type of value based on the property.
     *
     * @param Property $property
     * @return string
     */
    public function property($property)
    {
        return $this->getClassName($property);
    }

    /**
     * Resolves the property classpath.
     *
     * @param $property
     * @return PropertyAbstract
     */
    public function getClassName($property)
    {
        $type = $this->getPropertyType($property);

        if (is_null($type) || ! isset(static::$valueTypes[$type])) {
            throw new RuntimeException('Error when resolving unregisterd property type');
        }

        return static::$valueTypes[$type];
    }

    /**
     * Get the type of a property.
     *
     * @param $property
     * @return mixed
     */
    protected function getPropertyType($property)
    {
        if (is_string($property)) {
            return $property;
        }

        return $property instanceof Property ?
            $property->getAttribute('type') : null;
    }
}
