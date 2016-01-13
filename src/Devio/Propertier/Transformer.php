<?php

namespace Devio\Propertier;

class Transformer
{
    /**
     * The resolver instance.
     *
     * @var Resolver
     */
    protected $resolver;

    /**
     * Transformer constructor.
     *
     * @param Resolver $resolver
     */
    public function __construct($resolver = null)
    {
        $this->resolver = $resolver;
    }

    /**
     * Perform the transformation either single or collection values.
     *
     * @param $properties
     */
    public function transform($properties)
    {
        // We will iterate throught every given property replacing the values
        // relation with the transformed elements. These transformed values
        // might result into a collection (if multiple) or simple object.
        foreach ($properties as $property) {
            $property->setRelation(
                'values', $this->transformValues($property->values, $property)
            );
        }

        return $properties;
    }

    /**
     * Transform a raw value into a property type value.
     *
     * @param $values
     * @param $property
     * @return PropertyValue
     */
    protected function transformValues($values, $property)
    {
        $resolver = $this->getResolver();
        // We have to iterate every value in the collection to transform it from
        // Value to the right [Property]Value object. It will return an object
        // of a different type with the same attributes as the original item.
        $result = $values->map(function ($value) use ($property, $resolver) {
            return $resolver->value($property, $value->getAttributes(), true);
        });

        // If the property we are transforming can store has been set as multi-
        // value, we can return the full collection. Otherwise we'll extract
        // the first value of the collection and will omit if any other.
        return $property->isMultivalue()
            ? $result
            : $result->first();
    }

    /**
     * Get the resolver instance or create new.
     *
     * @return Resolver
     */
    public function getResolver()
    {
        return $this->resolver = $this->resolver ? : new Resolver();
    }

    /**
     * Set the resolver instance.
     *
     * @param Resolver $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }
}
