<?php

namespace Devio\Eavquent;

use Closure;
use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\Attribute\Attribute;
use Devio\Eavquent\Entity\EntityBootingScope;
use Illuminate\Contracts\Container\Container;

trait EntityAttributeValues
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The manager instance.
     *
     * @var Manager
     */
    protected $attributeManager;

    /**
     * The attributes related to the entity.
     *
     * @var
     */
    protected static $entityAttributes;

    /**
     * The attribute relations closures.
     *
     * @var array
     */
    protected $attributeRelations = [];

    /**
     * Set if the relations have been booted.
     *
     * @var bool
     */
    protected $attributeRelationsBooted = false;

    /**
     * Booting the trait.
     */
    public static function bootEntityAttributeValues()
    {
        $instance = new static;
        $manager = $instance->getAttributeManager();

        $attributes = $manager->get($instance->getMorphClass());
        static::$entityAttributes = $attributes->groupBy(Attribute::COLUMN_CODE);

        static::addGlobalScope(new ParseWithScope);
    }

    /**
     * Booting the registered attributes as relations.
     */
    public function bootEavRelationsIfNotBooted()
    {
        if ($this->attributeRelationsBooted) {
            return;
        }

        foreach ($this->getEntityAttributes()->flatten() as $attribute) {
            $relation = $this->getAttributeRelationClosure($attribute);

            array_set($this->attributeRelations, $attribute->getCode(), $relation);
        }

        $this->attributeRelationsBooted = true;
    }

    /**
     * Get the entity attributes.
     *
     * @return mixed
     */
    public function getEntityAttributes()
    {
        return static::$entityAttributes;
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string $key
     * @return bool
     */
    public function isGetRawAttributeMutator($key)
    {
        return (bool) preg_match('/^raw(\w+)object$/i', $key);
    }

    /**
     * Remove any mutator prefix and suffix.
     *
     * @param $key
     * @return mixed
     */
    protected function clearGetRawAttributeMutator($key)
    {
        return $this->isGetRawAttributeMutator($key) ?
            camel_case(str_ireplace(['raw', 'object'], ['', ''], $key)) : $key;
    }

    public function getAttribute($key)
    {
        if ($this->isEntityAttribute($key)) {
            return $this->getRelationshipFromMethod($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Get the model attribute relations.
     *
     * @return array
     */
    public function getAttributeRelations()
    {
        return static::$attributeRelations;
    }

    /**
     * Set an attribute relation.
     *
     * @param $relation
     * @param $value
     * @return $this
     */
    public function setAttributeRelation($relation, $value)
    {
        $this->attributeRelations[$relation] = $value;

        return $this;
    }

    /**
     * Set the attribute manager instance.
     *
     * @param $manager
     * @return $this
     */
    public function setAttributeManager($manager)
    {
        $this->attributeManager = $manager;

        return $this;
    }

    /**
     * @return Manager
     */
    public function getAttributeManager()
    {
        if (is_null($this->attributeManager)) {
            $this->setAttributeManager($this->getContainer()->make(Manager::class));
        }

        return $this->attributeManager;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (is_null($this->container)) {
            $this->container = \Illuminate\Container\Container::getInstance();
        }

        return $this->container;
    }

    /**
     * Dynamically pipe calls to attribute relations.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->bootEavRelationsIfNotBooted();

        if (isset($this->attributeRelations[$method])) {
            return call_user_func_array($this->attributeRelations[$method], $parameters);
        }

        return parent::__call($method, $parameters);
    }
}