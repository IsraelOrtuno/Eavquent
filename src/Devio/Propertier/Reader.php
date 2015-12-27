<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Devio\Propertier\Exceptions\PropertyNotFoundException;

class Reader
{
    /**
     * @var Collection
     */
    protected $properties;

    /**
     * @var Collection
     */
    protected $values;

    /**
     * Will provide the PropertyValue model of the key passed.
     *
     * @param $key
     * @return mixed|null
     */
    public function read($key)
    {
        $property = $this->findProperty($key);

        return $property->values;
//        dd($property->values->where('property_id', 2));
//        $values = $this->findValues($property);

        // Once we know what are the PropertyValues related to the property,
        // we'll decide if returning a collection or just a single value.
        // The colleciton is implicit due values is a hasMany relation.
//        if (! $property->isMultivalue()) {
//            $values = $values->count() ? $values->first() : null;
//        }

//        return $values;
    }

    /**
     * Will return the right property model that matches the key name.
     *
     * @param $key
     * @return Property
     */
    public function findProperty($key)
    {
        $finder = new PropertyFinder($this->properties);

        return $finder->find($key);
    }

    /**
     * Get the values based on a property given.
     *
     * @param $property
     * @return Collection
     */
//    public function findValues(Property $property)
//    {
//        $finder = new ValueFinder($this->values);
//
//        return $finder->find($property);
//    }

    /**
     * @param Collection $properties
     * @return $this
     */
    public function properties(Collection $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @param Collection $values
     * @return $this
     */
    public function values(Collection $values)
    {
        $this->values = $values;

        return $this;
    }
}
