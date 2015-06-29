<?php namespace Devio\Propertier\Observers;

use Devio\Propertier\Models\PropertyValue;
use Devio\Propertier\Properties\PropertyFactory;

class PropertyValueObserver {

    /**
     * Will only allow saving in if the value passes validation.
     *
     * @param PropertyValue $model
     * @return bool
     */
    public function saving(PropertyValue $model)
    {
        // If the model didn't change, not further checks should be made
        if ( ! $model->isDirty()) return false;

        // Fetching the property related to the value if available. This will
        // reduce the number of database queries as every saved value would
        // execute a query in order to fetch the property before storing.
        $property = $model->getPropertyRelation() ?: $model->property;

        return PropertyFactory::make($property)
                              ->value($model)
                              ->isValidForStorage();
    }
}