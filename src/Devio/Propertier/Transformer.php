<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;

class Transformer
{
    /**
     * The value or collection.
     *
     * @var Collection|PropertyValue
     */
    protected $values;

    /**
     * The property object.
     *
     * @var Property
     */
    protected $properties;

    /**
     * Perform the transformation either single or collection values.
     *
     * @return Collection|PropertyValue
     */
    public function transform()
    {
        $this->linkValuesToProperties();
        $transformed = collect();

        foreach ($this->properties as $property) {
            $transformed->push($this->transformValues($property->values, $property));
        }

        return $transformed;
    }

    /**
     * Transform a single property value.
     *
     * @param $values
     * @param $property
     * @return PropertyValue
     */
    public function transformValues($values, $property)
    {
        $resolver = new Resolver();

        // We have to iterate every value in the collection to transform it from
        // Value to the right [Property]Value object. It will return an object
        // of a different type with the same attributes as the original item.
        $result = $values->map(
            function ($value) use ($property, $resolver) {
                return $resolver->property($property, $value->getAttributes());
            }
        );

        // If the property we are transforming can store has been set as multi-
        // value, we can return the full collection. Otherwise we'll extract
        // the first value of the collection and will omit if any other.
        return $property->isMultivalue()
            ? $result
            : $result->first();
    }

    /**
     * Set the value/es to transform.
     *
     * @param PropertyValue|Collection $values
     * @return PropertyTransformer
     */
    public function values($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Set the properties to transform to.
     *
     * @param Property $properties
     * @return PropertyTransformer
     */
    public function properties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    protected function linkValuesToProperties()
    {
        (new ValueLinker)->values($this->values)
            ->properties($this->properties)
            ->link();
    }
}
