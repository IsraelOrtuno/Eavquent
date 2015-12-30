<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

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
     * Make a new instance.
     *
     * @param $values
     * @return static
     */
    public static function make($values)
    {
        return new static($values);
    }

    /**
     * Set the values.
     *
     * @param Collection $values
     * @return ValueFinder
     */
    public function values($values)
    {
        if (is_array($values)) {
            $values = collect($values);
        }

        $this->values = $values;

        return $this;
    }

    /**
     * Get the values based on a given property.
     *
     * @param $property
     * @return Collection
     */
    public function find($property)
    {
        if ($property instanceof Model) {
            $property = $property->getKey();
        }

        // Will filter through the values collection looking for those values that
        // are matching the property passed as parameter. The where method gets
        // the current property ID and return the values of same property_id.
        return $this->values->where((new Property)->getForeignKey(), $property);
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
