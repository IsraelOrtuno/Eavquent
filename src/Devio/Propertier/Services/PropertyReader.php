<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Exceptions\PropertyNotFoundException;
use Devio\Propertier\Propertier;

class PropertyReader
{
    /**
     * The entity model.
     *
     * @var Propertier
     */
    protected $entity;

    /**
     * @var PropertyFinder
     */
    private $finder;

    /**
     * PropertyReader constructor.
     *
     * @param Propertier     $entity
     * @param PropertyFinder $finder
     */
    public function __construct(Propertier $entity, PropertyFinder $finder)
    {
        $this->entity = $entity;
        $this->finder = $finder;
    }

    /**
     * Will provide the PropertyValue model of the key passed.
     *
     * @param $key
     *
     * @return mixed|null
     * @throws PropertyNotFoundException
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

        return is_null($values)
            ? $values
            : $this->transformValues($values, $property);
    }

    /**
     * Transform the colleciton of values into the right property objects.
     *
     * @param $values
     * @param $property
     *
     * @return mixed
     */
    protected function transformValues($values, $property)
    {
        return (new PropertyTransformer($values, $property))->transform();
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
        return $this->finder->entity($this->entity)
                            ->find($key);
    }

    /**
     * Finds the values based on a property given.
     *
     * @param $property
     *
     * @return mixed
     */
    protected function findValues($property)
    {
        return $this->entity->getValuesOf($property);
    }
}