<?php

namespace Devio\Eavquent;

use Closure;
use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Model;

class RelationBuilder
{
    /**
     * Build the relations for the entity attributes.
     *
     * @param Model $entity
     */
    public function build(Model $entity)
    {
        $attributes = $entity->getEntityAttributes();

        // We will manually add a relationship for every attribute registered
        // for this entity. Once we know the relation method we have to use
        // we will just add it to the attributeRelations class property.
        foreach ($attributes as $attribute) {
            $relation = $this->getRelationClosure($entity, $attribute);

            $entity->setAttributeRelation($attribute->code, $relation);
        }
    }

    /**
     * Generate the relation closure.
     *
     * @param Model $entity
     * @param Attribute $attribute
     * @return Closure
     */
    protected function getRelationClosure(Model $entity, Attribute $attribute)
    {
        $method = $method = $this->guessRelationMethod($attribute);

        // This will return a closure fully binded to the current model instance.
        // This will help us to simulate any relation as if it was handly made
        // in the original model class definition using a function statement.
        return Closure::bind(function () use ($entity, $attribute, $method) {
            $relation = $entity->$method($attribute->model, 'entity');

            // We add a where clausule in order to fetch only the elements that
            // are related to the given attribute. If no condition is set, it
            // will fetch all the value rows related to the current entity.
            return $relation->where($attribute->getForeignKey(), $attribute->getKey());
        }, $entity, get_class($entity));
    }

    /**
     * Get the relation name to use.
     *
     * @param Attribute $attribute
     * @return string
     */
    protected function guessRelationMethod(Attribute $attribute)
    {
        return $attribute->isCollection() ? 'morphMany' : 'morphOne';
    }
}
