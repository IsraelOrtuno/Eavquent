<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Property;
use Illuminate\Support\Collection;
use Devio\Propertier\PropertyValue;
use Devio\Propertier\PropertyBuilder;

class PropertyTransformer
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
     * PropertyTransformer constructor.
     *
     * @param $values
     * @param $property
     */
    public function __construct($values, $property)
    {
        $this->values = $values;
        $this->property = $property;
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
        $builder = $this->getBuilder();

        return $builder->make($this->property, $value->getAttributes());
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
     * Gets the property resolver.
     *
     * @return PropertyBuilder
     */
    protected function getBuilder()
    {
        return new PropertyBuilder;
    }
}