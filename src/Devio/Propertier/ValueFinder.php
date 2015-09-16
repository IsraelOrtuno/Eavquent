<?php
namespace Devio\Propertier\Finders;

use Devio\Propertier\Property;
use Illuminate\Support\Collection;

class ValueFinder
{
    /**
     * The values collection.
     *
     * @var Collection
     */
    protected $values;

    /**
     * ValueFinder constructor.
     *
     * @param $values
     */
    public function __construct($values = null)
    {
        $this->values($values);
    }

    /**
     * Set the values.
     *
     * @param Collection $values
     *
     * @return ValueFinder
     */
    public function values($values)
    {
        if (is_array($values))
        {
            $values = collect($values);
        }

        $this->values = $values;

        return $this;
    }

    /**
     * Get the values based on a property given.
     *
     * @param $property
     *
     * @return Collection
     */
    public function find(Property $property)
    {
        // Will filter through the values collection looking for those values that
        // are matching the property passed as parameter. The where method gets
        // the current property ID and return the values of same property_id.
        return $this->values->where(
            $property->getForeignKey(), $property->getKey()
        );
    }

    /**
     * Get the values.
     *
     * @return Collection
     */
    public function getValues()
    {
        return $this->values;
    }
}