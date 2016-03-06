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
     * @var Container
     */
    protected $container;

    /**
     * @var Manager
     */
    protected $attributeManager;

    /**
     * @var
     */
    protected static $entityAttributes;

    /**
     * @var array
     */
    protected $attributeRelations = [];

    /**
     * @var bool
     */
    protected $attributeRelationsBooted = false;

    /**
     * Booting the trait.
     */
    public static function bootEntityAttributeValues()
    {
        static::addGlobalScope(new EntityBootingScope);
    }

    /**
     * Booting the registered attributes as relations.
     */
    public function bootEavRelationsIfNotBooted()
    {
        if ($this->attributeRelationsBooted) {
            return;
        }

        $this->loadEntityAttributes();

        foreach ($this->getEntityAttributes()->flatten() as $attribute) {
            $relation = $this->getAttributeRelationClosure($attribute);

            array_set($this->attributeRelations, $attribute->getCode(), $relation);
        }

        $this->attributeRelationsBooted = true;
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        echo "<pre>", var_dump('newFromBuilder'), "</pre>";
        return parent::newFromBuilder($attributes, $connection);
    }

    /**
     * Load the attributes related to this entity.
     *
     * @return mixed
     */
    public function loadEntityAttributes()
    {
        // TODO: remove refresh
        $attributes = $this->getAttributeManager()->refresh()->get($this->getMorphClass());

        static::$entityAttributes = $attributes->groupBy(Attribute::COLUMN_CODE);
    }

    public function getEntityAttributes()
    {
        return static::$entityAttributes;
    }

    /**
     * Check if the key corresponds to an entity attribute.
     *
     * @param $key
     */
    public function isEntityAttribute($key)
    {
        return $this->getEntityAttributes()->has($key);
    }

    /**
     * Generate the relation closure.
     *
     * @param $attribute
     * @return Closure
     */
    protected function getAttributeRelationClosure(Attribute $attribute)
    {
        // This will return a closure fully binded to the current model instance.
        // This will help us to simulate any relation as if it was handly made
        // in the original model class definition using a function statement.
        return Closure::bind(function () use ($attribute) {
            $method = $this->getAttributeRelationMethod($attribute);

            $relation = $this->$method($attribute->getModelClass(), Attribute::COLUMN_ENTITY);

            // We add a where clausule in order to fetch only the elements that
            // are related to the given attribute. Without this condition it
            // would fetch all the values related to the entity
            return $relation->where($attribute->getForeignKey(), $attribute->getKey());
        }, $this, get_class());
    }

    /**
     * Get the relation name to use.
     *
     * @param Attribute $attribute
     * @return string
     */
    protected function getAttributeRelationMethod(Attribute $attribute)
    {
        return $attribute->isCollection() ? 'morphMany' : 'morphOne';
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
        return $this->getContainer()->make(Manager::class);
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