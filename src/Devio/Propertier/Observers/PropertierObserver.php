<?php
namespace Devio\Propertier\Observers;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Services\ValueSetter;

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
                                 ->set($key, $value);

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
        foreach ($model->values as $value)
        {
            // Will iterate thorugh every model value and save any change made.
            // Just in case the parent model has just been created, we will
            // force to relate it now as it didn't have an id till saved.
            if ($value->isDirty())
            {
                $value->relatedOrRelateTo($model->id);
                $value->save();
            }
        }

        // Once we have all our values correcly stored, we will clear the old
        // values corresponding to any previous collection items stored in
        // the database. This will delete all them. Only for collections.
        $model->executeDeletionQueue();
    }
}