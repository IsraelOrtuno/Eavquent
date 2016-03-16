<?php

namespace Devio\Eavquent;

use Illuminate\Database\Eloquent\Model;

abstract class Query
{
    /**
     * The entity instance.
     *
     * @var Model
     */
    protected $entity;

    /**
     * The entity attributes.
     *
     * @var Collection
     */
    protected $attributes;

    /**
     * Query constructor.
     *
     * @param Model $entity
     */
    public function __construct(Model $entity)
    {
        $this->entity = $entity;
        $this->attributes = $entity->getEntityAttributes();
    }

    /**
     * Check if the key is an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function isAttribute($key)
    {
        return $this->attributes->has($key);
    }

    /**
     * Get an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes->get($key);
    }
}

// TODO: add schema column check here.