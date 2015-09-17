<?php
namespace Devio\Propertier;

use Devio\Propertier\Property;
use Illuminate\Support\Collection;
use Devio\Propertier\PropertyValue;
use Devio\Propertier\PropertyBuilder;

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
    protected $property;

    /**
     * @var PropertyBuilder
     */
    private $builder;

    /**
     * PropertyTransformer constructor.
     *
     * @param PropertyBuilder $builder
     */
    public function __construct(PropertyBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Perform the transformation either single or collection values.
     *
     * @return Collection|PropertyValue
     */
    public function transform()
    {
        if ($this->values instanceof Collection)
        {
            return $this->transformCollection();
        }

        return $this->transformOne($this->values);
    }

    /**
     * Transform a single property value.
     *
     * @param PropertyValue $value
     *
     * @return PropertyValue
     */
    public function transformOne(PropertyValue $value)
    {
        return $this->builder->make($this->property, $value->getAttributes());
    }

    /**
     * Trasforming a entire values collection.
     *
     * @return Collection
     */
    public function transformCollection()
    {
        return $this->values->map(function(PropertyValue $item)
        {
            return $this->transformOne($item);
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
     * Set the property to transform to.
     *
     * @param Property $property
     *
     * @return PropertyTransformer
     */
    public function property($property)
    {
        $this->property = $property;

        return $this;
    }

}