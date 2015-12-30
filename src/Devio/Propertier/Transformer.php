<?php

namespace Devio\Propertier;

class Transformer
{
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
            $transformed = $this->transformValues($property->values, $property);
            $property->setRelation('values', $transformed);
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
        $resolver = new Resolver();

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
}
