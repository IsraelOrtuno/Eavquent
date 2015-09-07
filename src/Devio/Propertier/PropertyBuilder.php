<?php
namespace Devio\Propertier;

use Devio\Propertier\Exceptions\UnresolvedPropertyException;

class PropertyBuilder
{
    /**
     * Get the right property type class based on the property provided.
     *
     * @param       $property
     * @param array $attributes
     *
     * @return PropertyAbstract
     */
    public function make($property, $attributes = [])
    {
        $class = $this->resolve($property);

        // Will create a new PropertyAbstract model based on the property passed
        // as argument. It will also fill the model if a set of attributes is
        // provided and relate it to the property, eager loading included.
        $propertyValue = new $class($attributes);

        return $propertyValue;
    }

    /**
     * Resolves the property classpath.
     *
     * @param $property
     *
     * @return PropertyAbstract
     * @throws UnresolvedPropertyException
     */
    protected function resolve($property)
    {
        if (is_string($property))
        {
            $type = $property;
        }
        elseif ($property instanceof Property)
        {
            $type = $property->getAttribute('type');
        }
        else
        {
            throw new UnresolvedPropertyException;
        }

        return config("propertier.properties.$type");
    }
}