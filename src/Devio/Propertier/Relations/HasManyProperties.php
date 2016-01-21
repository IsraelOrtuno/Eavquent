<?php

namespace Devio\Propertier\Relations;

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
     * @param Builder $query
     * @param Model $model
     * @param string $entity
     */
    public function __construct(Builder $query, Model $model, $entity)
    {
        $this->entity = $entity;
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

        $values = $this->getParent()->exists ?
            $this->getParent()->values : new Collection;

        // Loading the values for every property. This task will be automatically
        // run by the property class. We need to pass the collection of values
        // this entity. After return the name keyed properties collection.
        foreach ($properties as $property) {
            $property->loadValues($values);
        }

        return $properties->keyby('name');
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
     * @param string $type
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        // We will only load the values relation for all the models if there is
        // at least one property registered for them. It is pointless to load
        // the values if there aren't properties so we improve performance.
        if (count($results)) {
            with(new Collection($models))->load('values');
        }

        foreach ($models as $model) {
            $properties = $results->map(function ($property) use ($model) {
                // Replicating the existing property will avoid the problem of
                // many enities pointing to the same property object. Every
                // entity should have its stand-alone property instances.
                return $property->replicateExisting()->loadValues($model->values);
            });

            $model->setRelation($relation, $properties->keyBy('name'));
        }

        return $models;
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
