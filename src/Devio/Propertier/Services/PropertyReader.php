<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Propertier;

class PropertyReader
{
    /**
     * @var Propertier
     */
    private $entity;

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

    protected function findProperty($key)
    {
        $properties = $this->entity->getPropertiesKeyedBy('name');

        return $properties->get($key);
    }

    protected function findValues($property)
    {
        return $this->entity->values->where(
            $property->getForeignKey(), $property->getKey()
        );
    }
}