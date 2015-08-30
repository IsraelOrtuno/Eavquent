<?php
namespace Devio\Propertier\Observers;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Services\ValueSetter;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Devio\Propertier\Jobs\ClearPreviousValues;

class PropertyObserver
{
    use DispatchesJobs;

    /**
     * Catching the saving event. Before saving, the properties
     * values will be established to their set values.
     *
     * @param $model
     * @return bool
     */
    public function saving(Model $model)
    {
        // Dinamically will eager load the entity properties and its values.
        // By iterating them, we will just assign the property values to
        // the property (being this handled by the ValueSetter class).
        $model = $this->eagerLoadRelations($model);
        $valueSetter = ValueSetter::make($model);

        foreach ($model->getAttributes() as $key => $value)
        {
            if ($model->isProperty($key))
            {
                $valueSetter->assign($key, $value);

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
     * Review: Considder using queues for cleaning existing values.
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
            $property->push();
        }

        // We are currently performing this task in a Job class as it is
        // perfectly functional and wanted to try the new queue system
        // that Laravel 5.1 introduced. This might change in future.
        $this->dispatch(new ClearPreviousValues($model));
    }

    /**
     * Will eager load the model relations with a condition. This
     * condition will only load the values related to the current
     * entity. If not condition is set, it will eager load every
     * value related to the entity.
     *
     * @param $model
     * @return mixed
     */
    protected function eagerLoadRelations($model)
    {
        $relations = ['properties.values' => function ($query) use ($model)
        {
            $query->where('entity_type', $model->getMorphClass())
                  ->where('entity_id', $model->id);
        }];

        return $model->load($relations);
    }
}