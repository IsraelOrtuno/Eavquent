<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;

class PropertyFinder
{
    /**
     * Collection of properties.
     *
     * @var Collection
     */
    protected $properties;

    /**
     * PropertyFinder constructor.
     *
     * @param null $properties
     */
    public function __construct($properties = null)
    {
        $this->properties($properties);
    }

    /**
     * Sets the property collection.
     *
     * @param $properties
     * @return $this
     */
    public function properties($properties)
    {
        if (is_array($properties)) {
            $properties = collect($properties);
        }

        $this->properties = $properties;

        return $this;
    }

    /**
     * Will return the right property model that matches the key name.
     *
     * @param $name
     * @return Property
     */
    public function find($name)
    {
        if (! $this->properties) {
            return null;
        }

        // We will key our collection by name, this way will be much easier for
        // filtering. Once keyed, just checking if the property has a key of
        // the name passed as argument will mean that a property exists.
        $properties = $this->properties->keyBy('name');

        return $properties->has($name)
            ? $properties->get($name)
            : null;
    }

    /**
     * Get the properties.
     *
     * @return Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
