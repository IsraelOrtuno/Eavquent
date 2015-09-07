<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Propertier;

class PropertyReader
{
    /**
     * @var Propertier
     */
    protected $entity;

    /**
     * PropertyReader constructor.
     *
     * @param Propertier $entity
     */
    public function __construct(Propertier $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Will provide the PropertyValue model of the key passed.
     *
     * @param $key
     *
     * @return null
     */
    public function read($key)
    {
        $property = $this->findProperty($key);
        $values = $this->findValues($property);

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
     * @return mixed
     */
    protected function findProperty($key)
    {
        $properties = $this->entity->getPropertiesKeyedBy('name');

        return $properties->get($key);
    }

    /**
     * Finds the right value based on a property given.
     *
     * @param $property
     *
     * @return mixed
     */
    protected function findValues($property)
    {
        // Will filter through the values collection looking for those values that
        // are matching the property passed as parameter. The where method gets
        // the current property ID and return the values of same property_id.
        return $this->getValues()->where(
            $property->getForeignKey(), $property->getKey()
        );
    }

    /**
     * Will return the entity values collection.
     *
     * @return mixed
     */
    protected function getValues()
    {
        return $this->entity->values;
    }
}