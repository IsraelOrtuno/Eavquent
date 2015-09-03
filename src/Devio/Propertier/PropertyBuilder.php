<?php
namespace Devio\Propertier;

class PropertyBuilder
{
    /**
     * Get the right property type class based on the property provided.
     *
     * @param Property $property
     * @param array    $attributes
     *
     * @return PropertyAbstract
     */
    public function make(Property $property, $attributes = [])
    {
        $class = $this->resolve($property);

        // Will create a new PropertyAbstract model based on the property passed
        // as argument. It will also fill the model if a set of attributes is
        // provided and relate it to the property, eager loading included.
        $propertyValue = new $class($attributes);

        $propertyValue->setRelation('property', $property);

        return $propertyValue;
    }

    /**
     * Resolves the property classpath.
     *
     * @param Property $property
     *
     * @return PropertyAbstract
     */
    protected function resolve(Property $property)
    {
        return config("propertier.properties.{$property->type}");
    }
}