<?php

namespace Devio\Eavquent;

use Illuminate\Support\Str;
use Devio\Eavquent\Value\Value;
use Devio\Eavquent\Value\Builder;
use Devio\Eavquent\Value\Collection;
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
        $entity->bootEavquentIfNotBooted();

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
            return $value->pluck('content');
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

        if ($this->entity->relationLoaded($key)) {
            return $this->entity->getRelation($key);
        }

        return $this->entity->getRelationValue($key);
    }

    /**
     * Set the content of the given attribute.
     *
     * @param $key
     * @param $value
     * @return $this|mixed
     */
    public function set($key, $value)
    {
        $current = $this->getRawContent($key);
        $attribute = $this->getAttribute($key);

        // $current will always contain a collection when an attribute is multivalued
        // as morphMany provides collections even if no values were matched, making
        // us assume at least an empty collection object will be always provided.
        if ($attribute->isCollection()) {
            if (is_null($current)) {
                $this->entity->setRelation($key, $current = new Collection);
            }

            $current->replace($value);

            return $this;
        }

        // If the attribute to set is a collection, it will be replaced by the
        // new value. If the value model does not exist, we will just create
        // and set a new value model, otherwise its value will get updated.
        if (is_null($current)) {
            return $this->setContent($attribute, $value);
        }

        return $this->updateContent($current, $value);
    }

    /**
     * Set the content of an unexisting value.
     *
     * @param $attribute
     * @param $value
     * @return mixed
     */
    protected function setContent($attribute, $value)
    {
        if (! $value instanceof Value) {
            $value = $this->builder->build($this->entity, $attribute, $value);
        }

        return $this->entity->setRelation(
            $attribute->getCode(), $value
        );
    }

    /**
     * Update the content of an existing value.
     *
     * @param $current
     * @param $new
     * @return mixed
     */
    protected function updateContent($current, $new)
    {
        if ($new instanceof Value) {
            $new = $new->getContent();
        }

        return $current->setContent($new);
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
