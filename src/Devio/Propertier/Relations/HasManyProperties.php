<?php
namespace Devio\Propertier\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyProperties extends HasMany
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @param Builder $query
     * @param Model   $model
     * @param string  $entity
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
        if (static::$constraints)
        {
            $this->query->where($this->foreignKey, '=', $this->getParentKey());
            $this->query->whereNotNull($this->foreignKey);
        }
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param array      $models
     * @param Collection $results
     * @param string     $relation
     * @param string     $type
     *
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        foreach ($models as $model)
        {
            // A simple trick will make after filtering easier. Setting
            // the property name as key will help to find the element
            // much easier than having to filter through collection.
            $model->setRelation(
                $relation, $results->keyBy('name')
            );
        }

        return $models;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array $models
     *
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