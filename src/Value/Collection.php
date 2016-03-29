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
        $this->entity = $entity;
        $this->attribute =$attribute;
    }
}
