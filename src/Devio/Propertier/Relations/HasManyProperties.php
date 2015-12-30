<?php

namespace Devio\Propertier\Relations;

use Devio\Propertier\ValueLinker;
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
        // We first have to load the entity values relation which will fetch
        // all the values available for every entity model. We can now use
        // these values to link them to the right property of the model.
        with(new Collection($models))->load('values');

        foreach ($models as $model) {
            // We have to get a clone of every property and get its values linked
            // for loop iteration. The relation we are setting will include the
            // property and its values already linked simulating eagerloading.
            $linked = $results->map(function ($result) {
                return $result->replicateExisting();
            });

            $model->setRelation($relation, $linked);

            ValueLinker::make($model->properties, $model->values)->link();
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
