<?php

namespace Devio\Eavquent;

use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\Entity\ParseWithScope;
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
     * The attribute reader instance.
     *
     * @var ReadQuery
     */
    protected $readQuery;

    /**
     * The attribute setter instance.
     *
     * @var WriteQuery
     */
    protected $writeQuery;

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
    public $attributeRelationsBooted = false;

    /**
     * Booting the trait.
     */
    public static function bootEntityAttributeValues()
    {
        $instance = new static;
        $manager = $instance->getAttributeManager();

        $attributes = $manager->get($instance->getMorphClass());
        static::$entityAttributes = $attributes->keyBy('code');

        static::addGlobalScope(new ParseWithScope);
    }

    /**
     * Booting the registered attributes as relations.
     */
    public function bootEavquentIfNotBooted()
    {
        if (! $this->attributeRelationsBooted) {
            $this->getRelationLoader()->load($this);
            $this->attributeRelationsBooted = true;
        }
    }

    /**
     * Creates a new instance and rebinds relations.
     *
     * @param array $attributes
     * @param bool $exists
     * @return mixed
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        $model->bootEavquentIfNotBooted();

        return $model;
    }

    /**
     * Get the relation value from attribute relations.
     *
     * @param $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        if (! is_null($result = parent::getRelationValue($key))) {
            return $result;
        }

        // In case any relation value is found, we will just provide it as is.
        // Otherwise, we will check if exists any attribute relation for the
        // given key. If so, we will load the relation calling its method.
        if ($this->isAttributeRelation($key)) {
            return $this->getRelationshipFromMethod($key);
        }
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
     * Get an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $query = $this->readQuery();

        return $query->isAttribute($key)
            ? $query->read($key) : parent::getAttribute($key);
    }

    /**
     * Get the ReadQuery instance.
     *
     * @return ReadQuery
     */
    public function readQuery()
    {
        return $this->readQuery = $this->readQuery ?: $this->getContainer()->make(ReadQuery::class, [$this]);
    }

    /**
     * Get the SetQuery instance.
     *
     * @return WriteQuery
     */
    public function writeQuery()
    {
        return $this->writeQuery = $this->writeQuery ?: $this->getContainer()->make(WriteQuery::class, [$this]);
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
     * Check if key is an attribute relation.
     *
     * @param $key
     * @return bool
     */
    public function isAttributeRelation($key)
    {
        return isset($this->attributeRelations[$key]);
    }

    /**
     * Get the attribute relations.
     *
     * @return array
     */
    public function getAttributeRelations()
    {
        $this->bootEavquentIfNotBooted();

        return $this->attributeRelations;
    }

    /**
     * Get the attribtue manager instance.
     *
     * @return Manager
     */
    public function getAttributeManager()
    {
        return $this->attributeManager ?: $this->getContainer()->make(Manager::class);
    }

    /**
     * Get the relation loader instance.
     *
     * @return RelationLoader
     */
    public function getRelationLoader()
    {
        return $this->getContainer()->make(RelationLoader::class);
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
        return $this->container = $this->container ?: \Illuminate\Container\Container::getInstance();
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
        $this->bootEavquentIfNotBooted();

        if ($this->isAttributeRelation($method)) {
            return call_user_func_array($this->attributeRelations[$method], $parameters);
        }

        return parent::__call($method, $parameters);
    }
}