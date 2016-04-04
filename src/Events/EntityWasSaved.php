<?php

namespace Devio\Eavquent\Events;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

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
        if (! $model->isAttributeRelationsBooted() || ! $model->autoPushEnabled()) {
            return;
        }

        $connection = $model->getConnection();
        $connection->beginTransaction();

        // If autopush is not enabled, we'll let the user handle the saving process.
        // When saving a model, we will also clear any trashed value that may be
        // queued for deletion for any reason: null, collection replacement...
        try {
            $this->save($model);
            $model->getTrash()->clear();
        } catch (\Exception $e) {
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
