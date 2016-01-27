<?php

namespace Devio\Propertier\Listeners;


class SavedEntity
{
    /**
     * Handling after saved.
     *
     * @param $model
     * @return bool
     */
    public function handle($model)
    {
        if (! $model->relationLoaded('properties')) {
            return true;
        }

        // When an entity is saved, we will iterate every property and perform
        // a push in every of them. Setting the entity of each property will
        // prevent us of trying to save a value for a not known entity id.
        foreach ($model->properties as $property) {
            $property->entity($model)->push();
        }
    }
}