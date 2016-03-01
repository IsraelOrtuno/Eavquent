<?php

namespace Devio\Propertier\Listeners;

use Illuminate\Support\Collection;

class PartnerSaved
{
    /**
     * Handling after saved.
     *
     * @param $partner
     * @return bool
     */
    public function handle($partner)
    {
        foreach ($partner->getFieldRelations() as $field) {
            // For every field relation, we will spin through every model and
            // push it in order to save. If any of the push operations fail
            // we will return false. This will rollback previous savings.
            $models = $partner->getRelationValue($field);

            $models = $models instanceof Collection
                ? $models->all() : [$models];

            // Passing the models array or collection through the array_filter
            // function will remove any null models and avoid performing any
            // operation to them.
            foreach (array_filter($models) as $model) {
                if (! $model->push()) {
                    return false;
                }
            }
        }
    }
}