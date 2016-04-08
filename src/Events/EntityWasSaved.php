<?php

namespace Devio\Eavquent\Events;

use Devio\Eavquent\Value\Builder;
use Devio\Eavquent\Value\Trash;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class EntityWasSaved
{
    /**
     * The trash instance.
     *
     * @var Trash
     */
    protected $trash;

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

        $this->trash = $model->getTrash();

        $connection = $model->getConnection();
        $connection->beginTransaction();

        // If autopush is not enabled, we'll let the user handle the saving process.
        // When saving a model, we will also clear any trashed value that may be
        // queued for deletion for any reason: null, collection replacement...
        try {
            $this->save($model);
            $this->trash->clear();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();

        $this->refresh($model);
    }

    /**
     * Saves the model values.
     *
     * @param Model $model
     */
    protected function save(Model $model)
    {
        $builder = new Builder;

        foreach ($model->getEntityAttributes() as $attribute) {
            if (! $model->relationLoaded($relation = $attribute->getCode())) {
                continue;
            }

            // We will ensure all values are truly linked to the entity record.
            // This is specially useful when creating new entity records as
            // we do not know its id until it is persisted into database.
            $values = $builder->ensure(
                $model, $attribute, $model->getRelationValue($relation)
            );

            // We will check for relation loads as we do not want to load any relation
            // which was not implicitly loaded. Then iterating over any value model
            // existing as relation and save it to persist it and its relations.
            $this->saveOrTrash($values);
        }
    }

    /**
     * Persists or trash the values.
     *
     * @param $values
     */
    protected function saveOrTrash($values)
    {
        $values = $values instanceof Collection
            ? $values->all() : [$values];

        // In order to provide flexibility and let the values have their own
        // relationships, here we'll check if a value should be completely
        // saved with its relations or just save its own current state.
        foreach ($values as $value) {
            if (is_null($value) || $this->trash($value)) {
                continue;
            }

            if ($value->shouldPush()) {
                $value->push();
            } else {
                $value->save();
            }
        }
    }

    /**
     * Trash the element if null.
     *
     * @param $value
     * @return bool
     */
    public function trash($value)
    {
        if (! is_null($value->getContent())) {
            return false;
        }

        $this->trash->add($value);

        return true;
    }

    /**
     * @param $model
     */
    protected function refresh($model)
    {
        foreach ($model->getEntityAttributes() as $attribute) {
            if ($attribute->isCollection()
                || ! $model->relationLoaded($relation = $attribute->getCode())
                || is_null($values = $model->getRelationValue($relation))
            ) {
                continue;
            }

            if (is_null($values->getContent())) {
                $model->setRelation($relation, null);
            }
        }
    }
}
