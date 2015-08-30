<?php
namespace Devio\Propertier\Properties;

use Devio\Propertier\Models\Property;

class Factory
{
    /**
     * Get the right property type class based on the property provided.
     *
     * @param Property $property
     * @return PropertyAbstract
     */
    public function make(Property $property)
    {
        $propertyType = $this->resolve($property);

        return $propertyType->setProperty($property);
    }

    /**
     * Resolves the property classpath and returns an instance of it.
     *
     * @param Property $property
     * @return mixed
     */
    protected function resolve(Property $property)
    {
        $class = __NAMESPACE__ . '\\' . studly_case($property->type) . 'Property';

        return new $class;
    }
}