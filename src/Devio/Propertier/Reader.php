<?php
namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Devio\Propertier\Finders\ValueFinder;
use Devio\Propertier\Finders\PropertyFinder;
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
     * @var PropertyFinder
     */
    private $propertyFinder;

    /**
     * @var ValueFinder
     */
    private $valueFinder;

    /**
     * PropertyReader constructor.
     *
     * @param PropertyFinder $propertyFinder
     * @param ValueFinder    $valueFinder
     */
    public function __construct(PropertyFinder $propertyFinder,
                                ValueFinder $valueFinder)
    {
        $this->propertyFinder = $propertyFinder;
        $this->valueFinder = $valueFinder;
    }

    /**
     * Will provide the PropertyValue model of the key passed.
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function read($key)
    {
        $property = $this->findProperty($key);
        $values = $this->findValues($property);

        // Once we know what are the PropertyValues related to the property,
        // we'll decide if returning a collection or just a single value.
        // The colleciton is implicit due values is a hasMany relation.
        if ( ! $property->isMultivalue())
        {
            $values = $values->count() ? $values->first() : null;
        }

        return $values;
    }

    /**
     * Will return the right property model that matches the key name.
     *
     * @param $key
     *
     * @return Property
     */
    public function findProperty($key)
    {
        return $this->propertyFinder->properties($this->properties)
                                    ->find($key);
    }

    /**
     * Get the values based on a property given.
     *
     * @param $property
     *
     * @return Collection
     */
    public function findValues(Property $property)
    {
        return $this->valueFinder->values($this->values)
                                 ->find($property);
    }

    /**
     * @param Collection $properties
     *
     * @return $this
     */
    public function properties(Collection $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @param Collection $values
     *
     * @return $this
     */
    public function values(Collection $values)
    {
        $this->values = $values;

        return $this;
    }
}