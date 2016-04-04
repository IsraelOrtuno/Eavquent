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
     *
     * @return Model
     */
    public function build(Model $entity, Attribute $attribute, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $instance = $attribute->getModelInstance();

        $this->ensure($entity, $attribute, $instance);

        return $instance->setContent($value);
    }

    /**
     * @param Model $entity
     * @param Attribute $attribute
     * @param Value $value
     *
     * @return Value
     */
    public function ensure(Model $entity, Attribute $attribute, Value $value)
    {
        $value->setAttribute('entity_id', $entity->getKey());
        $value->setAttribute($attribute->getForeignKey(), $attribute->getKey());

        return $value;
    }
}
