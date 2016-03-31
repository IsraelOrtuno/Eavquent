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

    public function link($entity, $attribute)
    {
        $this->setEntity($entity);
        $this->setAttribute($attribute);

        return $this;
    }

    public function replace($values)
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }

        $values = is_array($values) ? $values : func_get_args();

        $this->replaceCurrentItems();

        $this->items = $this->buildValues($values);
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

    protected function buildValues(array $values = [])
    {
        $builder = $this->getBuilder();

        // We will map the entire array of values and transform every item into
        // into the data type object linked of this collection. We will also
        // omit any model found so an user could also set models directly.
        return array_map(function ($value) use ($builder) {
            return $value instanceof Model ?
                $value : $builder->build($this->getEntity(), $this->getAttribute(), $value);
        }, $values);
    }

    /**
     * @return Builder
     */
    protected function getBuilder()
    {
        return new Builder;
    }

    /**
     * @return BaseCollection
     */
    public function getReplaced()
    {
        return $this->replaced;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }
}
