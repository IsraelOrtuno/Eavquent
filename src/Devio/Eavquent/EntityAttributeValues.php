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
    public $attributeManager;

    /**
     * @var array
     */
    protected static $attributeRelations = [];

    /**
     * @var bool
     */
    protected static $attributeRelationsBooted = false;

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
        if (static::$attributeRelationsBooted) {
            return;
        }

        $this->createAttributeManager();

        foreach ($this->getEntityAttributes() as $attribute) {
            $relation = $this->getAttributeRelationClosure($attribute);

            array_set(static::$attributeRelations, $attribute->getCode(), $relation);
        }

        static::$attributeRelationsBooted = true;
    }

    /**
     * Get the attributes related to this entity.
     *
     * @return mixed
     */
    protected function getEntityAttributes()
    {
        return $this->attributeManager->refresh()->get($this->getMorphClass());
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
        return (bool)preg_match('/^raw(\w+)object$/i', $key);
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
     * Get the attribute manager instance.
     *
     * @return AttributeManager
     */
    public function createAttributeManager()
    {
        if (is_null($this->attributeManager)) {
            $manager = $this->container->make(Manager::class);
            $this->setAttributeManager($manager);
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
     * Dynamically pipe calls to attribute relations.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset(static::$attributeRelations[$method])) {
            return call_user_func_array(static::$attributeRelations[$method], $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * TODO REMOVE
     *
     * @return array
     */
    public function getAttributeRelations()
    {
        return static::$attributeRelations;
    }
}