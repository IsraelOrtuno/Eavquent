<?php

namespace Devio\Eavquent\Value;

use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;

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

        $instance = $attribute->getTypeInstance();

        $this->ensure($entity, $attribute, $instance);

        return $instance->setContent($value);
    }

    /**
     * @param Model $entity
     * @param Attribute $attribute
     * @param $value
     *
     * @return Value
     */
    public function ensure(Model $entity, Attribute $attribute, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($value instanceof BaseCollection) {
            // If we receive a collection of values, we'll just spin through
            // them and recursively ensure they are properly linked to the
            // entity and attribute instances provided to this method.
            foreach ($value as $item) {
                $this->ensure($entity, $attribute, $item);
            }

            return $value;
        }

        // At any way we will try to find out the entity and attribute keys in
        // order to set them as foreign keys for the attribute. This way we
        // can make sure the value is properly linked to its "parents".
        $value->setAttribute('entity_id', $entity->getKey());
        $value->setAttribute($attribute->getForeignKey(), $attribute->getKey());

        return $value;
    }
}
