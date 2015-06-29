<?php namespace Devio\Propertier\Properties;

use Devio\Propertier\Models\Property;

class PropertyFactory {

    /**
     * Get the right property type class based on the property provided.
     *
     * @param Property $property
     * @return PropertyAbstract
     */
    public static function make(Property $property)
    {
        $class = __NAMESPACE__ . '\\' . studly_case($property->type);

        return new $class($property);
    }
}