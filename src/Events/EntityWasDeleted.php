<?php

namespace Devio\Eavquent\Events;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityWasDeleted
{
    /**
     * Handles the deletion of an entity.
     *
     * @param $model
     */
    public function handle($model)
    {
        $uses = class_uses_recursive(get_class($model));

        // We will initially check if the model is using soft deletes. If so,
        // the attribute values will remain untouched as they should sill
        // be available till the entity is truly deleted from database.
        if (in_array(SoftDeletes::class, $uses, true) && ! $model->isForceDeleting()) {
            return;
        }

        $this->delete($model);
    }

    /**
     * Delete any value for every attribute related to the entity.
     *
     * @param $model
     */
    protected function delete($model)
    {
        $attributes = $model->getEntityAttributes();

        foreach ($attributes as $attribute) {
            $this->performDeletion(
                $attribute->getType(), $model->getRelationValue($attribute->getName())
            );
        }
    }

    /**
     * Perform the deletion from the model class.
     *
     * @param $type The model class
     * @param $values The values to be deleted
     */
    protected function performDeletion($type, $values)
    {
        if (is_null($values)) {
            return;
        }

        if (! $values instanceof Collection) {
            $values = new Collection([$values]);
        }

        // Calling the `destroy` method from the given $type model class name
        // will finally delete the records from database if any was found.
        // We'll just provide an array containing the ids to be deleted.
        if ($values->count()) {
            $elements = $values->pluck('id');

            forward_static_call_array([$type, 'destroy'], [$elements->toArray()]);
        }
    }
}
