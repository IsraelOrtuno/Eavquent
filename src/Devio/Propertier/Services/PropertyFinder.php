<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Propertier;
use Devio\Propertier\Exceptions\PropertyNotFoundException;

class PropertyFinder
{
    /**
     * @var Propertier
     */
    protected $entity;

    public function entity(Propertier $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Will return the right property model that matches the key name.
     *
     * @param $key
     *
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function find($key)
    {
        $properties = $this->entity->getPropertiesKeyedBy('name');

        // If no property is found, this means we are trying to access a property
        // that is not registered to our entity, so we can't keep playing with
        // it. This will notify the problem throwing a not found exception.
        if ( ! $properties->has($key))
        {
            throw new PropertyNotFoundException;
        }

        return $properties->get($key);
    }

    /**
     * @return Propertier
     */
    public function getEntity()
    {
        return $this->entity;
    }
}