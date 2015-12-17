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
            $transformed->merge(
                $this->transformValuesIntoProperty($property->values, $property)
            );
        }

        dd($transformed);

        return $transformed;
    }

    /**
     * Transform a single property value.
     *
     * @param $values
     * @param $property
     *
     * @return PropertyValue
     */
    public function transformValuesIntoProperty($values, $property)
    {
        $builder = new Builder();

        return $values->map(function ($value) use ($property, $builder) {
            return $builder->make($property, $value->getAttributes());
        });
    }

    /**
     * Set the value/es to transform.
     *
     * @param PropertyValue|Collection $values
     *
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
     *
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
