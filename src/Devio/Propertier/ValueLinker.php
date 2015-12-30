<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Devio\Propertier\Exceptions\ValuesRelationAlreadyLoaded;

class ValueLinker
{
    /**
     * The properties collection.
     *
     * @var Collection
     */
    protected $properties;

    /**
     * The values collection.
     *
     * @var Collection
     */
    protected $values;

    /**
     * ValueLinker constructor.
     *
     * @param Collection $properties
     * @param Collection $values
     */
    public function __construct(Collection $properties, Collection $values)
    {
        $this->properties = $properties;
        $this->values = $values;
    }

    /**
     * Create a new instance.
     *
     * @param $properties
     * @param $values
     * @return static
     */
    public static function make($properties, $values)
    {
        return new static($properties, $values);
    }

    /**
     * Perform the link action.
     *
     * @return mixed
     */
    public function link()
    {
        return $this->properties instanceof Collection
            ? $this->linkMany()
            : $this->linkOne($this->properties);
    }

    /**
     * Link the values of a property.
     *
     * @param $property
     * @return mixed
     * @throws ValuesRelationAlreadyLoaded
     */
    protected function linkOne($property)
    {
        if ($property->relationLoaded('values')) {
            throw new ValuesRelationAlreadyLoaded;
        }
        // If the property already contains a values relationship, we do not
        // want to interfiere, this will be a breaking error. If not will
        // initialize the relation with the values that belong to it.
        $property->setRelation('values', $this->getValuesOfProperty($property));

        return $property;
    }

    /**
     * Link the values of many properties.
     *
     * @return mixed
     */
    protected function linkMany()
    {
        foreach ($this->properties as $property) {
            $this->linkOne($property);
        }

        return $this->properties;
    }

    /**
     * Get the values based on a given property.
     *
     * @param $property
     * @return Collection
     */
    public function getValuesOfProperty($property)
    {
        return ValueFinder::make($this->values)->find($property);
    }
}
