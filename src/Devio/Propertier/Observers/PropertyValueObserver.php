<?php
namespace Devio\Propertier\Observers;

use Devio\Propertier\Models\PropertyValue;
use Devio\Propertier\Properties\Factory as PropertyFactory;

class PropertyValueObserver
{
    /**
     * Will only allow saving if the value passes validation.
     *
     * @param PropertyValue $model
     *
     * @return bool
     */
    public function saving(PropertyValue $model)
    {
        if ( ! $model->isDirty()) return false;

        // If the model did not change, not further checks should be made.
        // Before saving storage, we have to validate that its value is
        // valid for storage using the property type class validator.
        $factory = new PropertyFactory();

        return $factory->make($model->property)
                       ->value($model)
                       ->isValidForStorage();
    }
}