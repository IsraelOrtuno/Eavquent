<?php

namespace Devio\Eavquent\Value;

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
        $instance = $this->getAttributeModelInstance($attribute);

        $instance->entity()->associate($entity);
        $instance->attribute()->associate($attribute);

        return $instance->setContent($value);
    }

    /**
     * Create the attribute model instance.
     *
     * @param Attribute $attribute
     * @return mixed
     */
    protected function getAttributeModelInstance(Attribute $attribute)
    {
        return new $attribute->getModel();
    }
}