<?php

namespace Devio\Eavquent\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntityWasSaved
{
    /**
     * Save values when an entity is saved.
     *
     * @param Model $model
     * @throws \Exception
     */
    public function handle(Model $model)
    {
        if (! $model->autoPushEnabled()) {
            return;
        }

        $connection = $model->getConnection();
        $connection->beginTransaction();

        // If autopush is not enabled, we'll let the user handle this using the
        // push() method. Otherwise we will just wrap this process within try
        // and catch in order to make sure all values are correctly saved.
        try {
            $this->save($model);
        }
        catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();
    }

    /**
     * Saves the model values.
     *
     * @param Model $model
     */
    protected function save(Model $model)
    {
        foreach ($model->getEntityAttributes() as $attribute) {
            if (! $model->relationLoaded($relation = $attribute->code)) {
                continue;
            }

            $values = $model->getRelationValue($relation);

            // We will check for relation loads as we do not want to load any relation
            // which was not implicity lodaded. Then iterating over any value model
            // existing as relation and save it to persist it and its relations.
            $this->saveOrPush($values);
        }
    }

    /**
     * Persists the values.
     *
     * @param $values
     */
    protected function saveOrPush($values)
    {
        $values = $values instanceof Collection
            ? $values->all() : [$values];

        // In order to provide flexibility and let the values have their own
        // relationships, here we'll check if a value should be completely
        // saved with its relations or just save its own current state.
        foreach (array_filter($values) as $value) {
            if ($value->shouldPush()) {
                $value->push();
            } else {
                $value->save();
            }
        }
    }
}
