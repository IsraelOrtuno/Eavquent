<?php

namespace Devio\Eavquent\Value;

use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Model;

class Builder
{
    /**
     * Create a new value instance.
     *
     * @param Model $entity
     * @param $attribute
     * @param $value
     */
    public function build(Model $entity, Attribute $attribute, $value)
    {
        $instance = $attribute->getModelInstance();

        $instance->entity()->associate($entity);
        $instance->attribute()->associate($attribute);

        return $instance->setContent($value);
    }
}