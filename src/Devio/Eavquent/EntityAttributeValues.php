<?php

namespace Devio\Eavquent;

use Closure;
use Devio\Eavquent\Attribute\Attribute;
use Devio\Eavquent\Entity\ParseWithScope;
use Illuminate\Contracts\Container\Container;
use Devio\Eavquent\Entity\EntityBootingScope;
use Devio\Eavquent\Attribute\AttributeManager;

trait Eavquent
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The
     *
     * @var EavquentManager
     */
    protected $eavquentManager;

    /**
     * The manager instance.
     *
     * @var AttributeManager
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

        $this->loadAttributes();

        foreach ($this->getEntityAttributes()->flatten() as $attribute) {
            $relation = $this->getAttributeRelationClosure($attribute);

            array_set($this->attributeRelations, $attribute->getCode(), $relation);
        }

        $this->attributeRelationsBooted = true;
    }

    /**
     * Load the attributes related to this entity.
     *
     * @return mixed
     */
    public function loadAttributes()
    {
        // TODO: remove refresh
        $attributes = $this->getAttributeManager()->refresh()->get($this->getMorphClass());

        static::$entityAttributes = $attributes->groupBy(Attribute::COLUMN_CODE);
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

    public function setEavquentManager($manager)
    {
        $this->eavquentManager = $manager;

        return $this;
    }

    public function getEavquentManager()
    {
        if (is_null($this->eavquentManager)) {
            $this->setEavquentManager($this->getContainer()->make(EavquentManager::class));
        }

        return $this->eavquentManager;
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
<<<<<<< HEAD:src/Devio/Eavquent/Eavquent.php
<<<<<<< HEAD:src/Devio/Eavquent/Eavquent.php
        if (is_null($this->attributeManager)) {
            $this->setAttributeManager($this->getContainer()->make(AttributeManager::class));
        }

        return $this->attributeManager;
=======
        return $this->getContainer()->make(Manager::class);
>>>>>>> parent of 0cc13cd... Few changes:src/Devio/Eavquent/EntityAttributeValues.php
=======
        return $this->getContainer()->make(Manager::class);
>>>>>>> parent of 0cc13cd... Few changes:src/Devio/Eavquent/EntityAttributeValues.php
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