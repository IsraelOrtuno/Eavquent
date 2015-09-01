<?php
namespace Devio\Propertier\Observers;

use Devio\Propertier\Services\ValueSetter;
use Illuminate\Database\Eloquent\Model;

class PropertierObserver
{
    /**
     * Catching the saving event. Before saving, the properties
     * values will be established to their set values.
     *
     * @param $model
     *
     * @return bool
     */
    public function saving(Model $model)
    {
        foreach ($model->getAttributes() as $key => $value)
        {
            if ($model->isProperty($key))
            {
                // If the current attribute is a property, we will just pass it
                // to the value setter class. This will add the values to the
                // property relations ready to be saved when using push.
                (new ValueSetter)->entity($model)
                                 ->assign($key, $value);

                // Unsetting the model property from the model attribute list
                // to make sure we are not trying to insert or update a db
                // field that does not exist in the real database schema.
                unset($model->{$key});
            }
        }

        return true;
    }

    /**
     * When the model is actually saved, the save method
     * of the properties values will be performed.
     *
     * @param $model
     */
    public function saved(Model $model)
    {
        // Will iterate through every model property and save any change made
        // We are iterating the entity properties due the "push" method is
        // causing infinite loop as every property is also related to an
        // entity. After saving, we'll clear the old values if needed.
        foreach ($model->properties as $property)
        {
            // Iterating every value to check if it is related to the model we
            // are going to save. This will handle the relation between new
            // created models that do not have an id assigned till saved.
            $property->values->map(function ($item, $key) use ($model)
            {
                $item->relatedOrRelateTo($model->id);
            });

            $property->push();
        }

        $model->executeDeletionQueue();
    }
}