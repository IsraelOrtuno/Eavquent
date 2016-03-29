<?php

namespace Devio\Eavquent\Value;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection
{
    /**
     * The entity the collection belongs to.
     *
     * @var
     */
    public $entity;

    /**
     * The attribute this collection is storing.
     *
     * @var
     */
    public $attribute;

    public function link($entity, $attribute)
    {
        $this->setEntity($entity);
        $this->setAttribute($attribute);
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
