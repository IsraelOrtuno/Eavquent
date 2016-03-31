<?php

namespace Devio\Eavquent\Value;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection
{
    /**
     * The entity the collection belongs to.
     *
     * @var Model
     */
    protected $entity;

    /**
     * The attribute this collection is storing.
     *
     * @var Attribute
     */
    protected $attribute;

    /**
     * The replaced items to be deleted.
     *
     * @var BaseCollection
     */
    protected $replaced;

    /**
     * Link the collection to entity and attribute.
     *
     * @param $entity
     * @param $attribute
     * @return $this
     */
    public function link($entity, $attribute)
    {
        $this->setEntity($entity);
        $this->setAttribute($attribute);

        return $this;
    }

    /**
     * Add new values to the collection.
     *
     * @param array $values
     * @return $this
     */
    public function add($values = [])
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }

        $values = is_array($values) ? $values : func_get_args();

        // Once we have made sure our input is an array of values, we will convert
        // them into value model objects (if no model instances are given). When
        // done we will just push all values into the current collection items.
        foreach ($values as $value) {
            $this->push($this->buildValue($value));
        }

        return $this;
    }

    /**
     * Queue for deletion current items and set news.
     *
     * @param $values
     * @return $this
     */
    public function replace($values = [])
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }

        $values = is_array($values) ? $values : func_get_args();

        // We will just store the current value items to the replaced collection
        // and replacing them with the new given values. These values will be
        // transformed into a data type value based on the linked attribute.
        $this->replaceCurrentItems();

        $this->items = $this->buildValues($values);

        return $this;
    }

    /**
     * Add current items to replaced collection.
     *
     * @return void
     */
    protected function replaceCurrentItems()
    {
        $items = array_filter($this->items, function ($item) {
            return $item->exists;
        });

        // We will add the current collection items to the replaced collection
        // which will be used for deleting this items from database if saved.
        // Filtering by exists will make sure we only store existing items.
        $this->replaced = is_null($this->replaced) ?
            new BaseCollection($items) : $this->replaced->merge($items);
    }

    /**
     * Build a value instance.
     *
     * @param $value
     * @return Model
     */
    public function buildValue($value)
    {
        if ($value instanceof Model) {
            return $value;
        }

        return $this->getBuilder()->build(
            $this->getEntity(), $this->getAttribute(), $value
        );
    }

    /**
     * Build value objects from array.
     *
     * @param array $values
     * @return mixed
     */
    public function buildValues(array $values = [])
    {
        $result = [];

        // We will map the entire array of values and transform every item into
        // into the data type object linked of this collection. We will also
        // omit any model found so an user could also set models directly.
        foreach ($values as $value) {
            $result[] = $this->buildValue($value);
        }

        return $result;
    }

    /**
     * Get the builder instance.
     *
     * @return Builder
     */
    protected function getBuilder()
    {
        return new Builder;
    }

    /**
     * Get the replaced values.
     *
     * @return BaseCollection
     */
    public function getReplaced()
    {
        return $this->replaced;
    }

    /**
     * Get the entity instance.
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the entity instance.
     *
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get the attribute instance.
     *
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set the attribute instance.
     *
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }
}
