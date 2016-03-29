<?php

namespace Devio\Eavquent;

use Devio\Eavquent\Value\Collection;
use Illuminate\Support\Str;
use Devio\Eavquent\Value\Builder;
use Illuminate\Database\Eloquent\Model;

class Interactor
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
     * The value builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Interactor constructor.
     *
     * @param Builder $builder
     * @param Model $entity
     */
    public function __construct(Builder $builder, Model $entity)
    {
        $this->entity = $entity;
        $this->attributes = $entity->getEntityAttributes();
        $this->builder = $builder;
    }

    /**
     * Check if the key is an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function isAttribute($key)
    {
        $key = $this->clearGetRawAttributeMutator($key);

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

    /**
     * Read the content of an attribute.
     *
     * @param $key
     * @return mixed|void
     */
    public function get($key)
    {
        if ($this->isGetRawAttributeMutator($key)) {
            return $this->getRawContent($key);
        }

        return $this->getContent($key);
    }

    /**
     * Get the content of the given attribute.
     *
     * @param $key
     * @return null
     */
    protected function getContent($key)
    {
        $value = $this->getRawContent($key);

        // In case we are accessing to a multivalued attribute, we will return
        // a collection with pairs of id and value content. Otherwise we'll
        // just return the single model value content as a plain result.
        if ($this->getAttribute($key)->isCollection()) {
            return $value;
        }

        return ! is_null($value) ? $value->getContent() : null;
    }

    /**
     * Get the raw content of the attribute (raw relationship).
     *
     * @param $key
     * @return mixed
     */
    protected function getRawContent($key)
    {
        $key = $this->clearGetRawAttributeMutator($key);

        $value = $this->entity->getRelationValue($key);

        // In case our value is a Collection (Eavquent), we will make sure the
        // links between the collection, entity and attribute are made so it
        // will be the only way the collection will know who it belongs to.
        if ($value instanceof Collection) {
            $value->link($this->entity, $this->getAttribute($key));
        }

        return $value;
    }

    public function set($key, $value)
    {
        $attribute = $this->getAttribute($key);


    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string $key
     * @return bool
     */
    protected function isGetRawAttributeMutator($key)
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
            Str::camel(str_ireplace(['raw', 'object'], ['', ''], $key)) : $key;
    }
}

// TODO: add schema column check here.

// TODO: When doing GET to a object, we have to link the
// TODO: attribute to the collection in order to be able
// TODO: to identify what type of attribute we are setting.
// TODO: Maybe we have to do same with entity as we do not
// TODO: know what is the entity we have to relate the value
// TODO: with.
