<?php

namespace Devio\Eavquent;

use Devio\Eavquent\Value\Trash;
use Devio\Eavquent\Value\Collection;
use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\Events\EntityWasSaved;
use Illuminate\Contracts\Container\Container;

trait Eavquent
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The interactor instance (set, get, isset).
     *
     * @var Interactor
     */
    protected $interactor;

    /**
     * The trash instance.
     *
     * @var Trash
     */
    protected $trash;

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
    public static function bootEavquent()
    {
        $instance = new static;
        $manager = $instance->getAttributeManager();

        $attributes = $manager->get($instance->getMorphClass());
        static::$entityAttributes = $attributes->keyBy('code');

        static::addGlobalScope(new EagerLoadScope);

        static::saved(EntityWasSaved::class . '@handle');
    }

    /**
     * Booting the registered attributes as relations.
     */
    public function bootEavquentIfNotBooted()
    {
        if (! $this->attributeRelationsBooted) {
            $this->trash = new Trash;

            $this->getRelationBuilder()->build($this);

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

//    public function attributesToArray()
//    {
//
//    }

    /**
     * Overwrite to link before setting when eager loading.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setRelation($key, $value)
    {
        if ($value instanceof Collection) {
            $this->linkRelationCollection($key, $value);
        }

        return parent::setRelation($key, $value);
    }

    /**
     * Get the relation value from attribute relations.
     *
     * @param $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        $result = parent::getRelationValue($key);

        // In case any relation value is found, we will just provide it as is.
        // Otherwise, we will check if exists any attribute relation for the
        // given key. If so, we will load the relation calling its method.
        if (is_null($result) && ! $this->relationLoaded($key) && $this->isAttributeRelation($key)) {
            $result = $this->getRelationshipFromMethod($key);
        }

        // When doing lazy loading, setRelation method is not triggered. Eloquent
        // just sets the result of the relation to the key into the $relations
        // array so we have to link the relation value collection here again.
        if (! is_null($result) && $result instanceof Collection) {
            $this->linkRelationCollection($key, $result);
        }

        // TODO: This could be removed when setRelation PR gets released.

        return $result;
    }

    /**
     * Links a Value collection to the current entity and attribute.
     *
     * @param $key
     * @param Collection $value
     */
    protected function linkRelationCollection($key, Collection $value)
    {
        $attributes = $this->getEntityAttributes();
        $value->link($this, $attributes->get($key));
    }

    /**
     * Check for auto pushing.
     *
     * @return bool
     */
    public function autoPushEnabled()
    {
        if (property_exists($this, 'autoPush')) {
            return $this->autoPush;
        }

        return true;
    }

    /**
     * Enable auto pushing.
     *
     * @return bool
     */
    public function enableAutoPush()
    {
        $this->autoPush = true;

        return $this;
    }

    /**
     * Disable auto pushing.
     *
     * @return bool
     */
    public function disableAutoPush()
    {
        $this->autoPush = false;

        return $this;
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
     * Set an attribute.
     *
     * @param $key
     * @param $value
     * @return $this|mixed
     */
    public function setAttribute($key, $value)
    {
        $interactor = $this->getInteractor();

        return $interactor->isAttribute($key) ?
            $interactor->set($key, $value) : parent::setAttribute($key, $value);
    }

    /**
     * Get an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $interactor = $this->getInteractor();

        return $interactor->isAttribute($key) ?
            $interactor->get($key) : parent::getAttribute($key);
    }

    /**
     * Get the morph type column.
     *
     * @return mixed
     */
    public function getMorphKeys()
    {
        return $this->getMorphs('entity', null, null);
    }

    /**
     * Get the interactor instance.
     *
     * @return Interactor
     */
    public function getInteractor()
    {
        return $this->interactor = $this->interactor
            ?: $this->getContainer()->make(Interactor::class, [$this]);
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
        return $this->getContainer()->make(Manager::class);
    }

    /**
     * Get the relation loader instance.
     *
     * @return RelationLoader
     */
    public function getRelationBuilder()
    {
        return $this->getContainer()->make(RelationBuilder::class);
    }

    /**
     * Set the container instance.
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get the container instance.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container = $this->container ?: \Illuminate\Container\Container::getInstance();
    }

    /**
     * Get the trash instance.
     *
     * @return Trash
     */
    public function getTrash()
    {
        return $this->trash;
    }

    /**
     * Check for attribute relations boot.
     *
     * @return bool
     */
    public function isAttributeRelationsBooted()
    {
        return $this->attributeRelationsBooted;
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
