<?php

namespace Devio\Propertier\Relations;

use Devio\Propertier\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyProperties extends HasMany
{
    /**
     * The entity class.
     *
     * @var string
     */
    protected $entity;

    /**
     * Property field to group by.
     *
     * @var string
     */
    protected $propertyGroup;

    /**
     * @param Builder $query
     * @param Model $model
     * @param string $entity
     */
    public function __construct(Builder $query, Model $model, $entity)
    {
        $this->entity = $entity;
        $this->propertyGroup = (new Property)->getForeignKey();
        parent::__construct($query, $model, 'entity', '');
    }

    /**
     * Get the relation results with loaded values.
     *
     * @return mixed
     */
    public function getResults()
    {
        // This will avoid loading the values relation when no properties were
        // found for the current entity. A bit of performance optimization.
        if (! count($properties = parent::getResults())) {
            return $properties;
        }

        // After having avoided loading the values relation when no properties
        // have been found we will just set the values related to the entity
        // and return the name-keyed properties collection as relation result.
        $this->setPropertyRelations($this->entity, $properties);

        return $properties->keyBy('name');
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->foreignKey, '=', $this->getParentKey());
        }
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param array $models
     * @param Collection $results
     * @param string $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        if (count($results)) {
            with(new Collection($models))->load('values');
        }

        // We will only load the values relation for all the models if there is
        // at least one property registered for them. The spin over entities
        // will set its values and set the name-keyed properties relation.
        foreach ($models as $entity) {
            $this->setPropertyRelations($entity, $results);

            $entity->setRelation($relation, $results->keyBy('name'));
        }

        return $models;
    }

    /**
     * @param $entity
     * @param $properties
     */
    protected function setPropertyRelations($entity, $properties)
    {
        $values = $entity->getRelationValue('values')->groupBy($this->propertyGroup);

        // TODO: Rethink this. Properties may be iterated just once as they are not
        // TODO: used for relating values anymore.
        foreach ($properties as $property) {
            if ($property->isMultivalue()) {

            }

            $entity->setRelation(
                $property->getName(), $values->get($property->getKey())
            );
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->where($this->foreignKey, $this->getParentKey());
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        return $this->entity;
    }
}
