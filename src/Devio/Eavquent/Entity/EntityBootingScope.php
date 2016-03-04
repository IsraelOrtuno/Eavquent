<?php

namespace Devio\Eavquent\Entity;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class EntityBootingScope implements Scope
{
    /**
     * Apply the scope.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $model->setContainer(Container::getInstance());

        $model->bootEavRelationsIfNotBooted();

        $this->parseEagerLoads($builder, $model);
    }

    /**
     * Parse eagerload for eav inclusions.
     *
     * @param Builder $builder
     * @param Model $model
     */
    protected function parseEagerLoads(Builder $builder, Model $model)
    {
        $eagerLoads = $builder->getEagerLoads();

        if (array_key_exists('eav', $eagerLoads)) {
            $eagerLoads = array_merge($eagerLoads, $model->getAttributeRelations());

            $builder->setEagerLoads(array_except($eagerLoads, 'eav'));
        }
    }


}