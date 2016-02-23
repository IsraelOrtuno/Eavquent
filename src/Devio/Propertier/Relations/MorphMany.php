<?php

namespace Devio\Propertier\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany as BaseMorphMany;

class MorphMany extends BaseMorphMany
{
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
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            $key = $model->getAttribute($this->localKey);

            // If we find any records for this model, we will pass for allocation.
            // Otherwise, we will provide an empty collection to initilaize the
            // relationships to null and empty collections depending on type.
            if (isset($dictionary[$key])) {
                $model->factory()->allocate(collect($dictionary[$key]));
            } else {
                $model->factory()->allocate(collect());
            }
        }

        return $models;
    }
}