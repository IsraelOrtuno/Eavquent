<?php
namespace Devio\Propertier\Services;

use Illuminate\Support\Collection;
use Devio\Propertier\PropertyBuilder;

class ValueFormatter
{
    /**
     * Format the property value model set.
     *
     * @param $model
     *
     * @return Collection
     */
    public function format($model)
    {
        if (is_null($model)) return null;

        // If null is passed as argument, it means that no values were found for
        // a non multivalue property. If it were an empty collection instead,
        // the formatMany method will return an empty collection as well.
        if (is_array($model) || $model instanceof Collection)
        {
            return $this->formatMany($model);
        }

        return $this->formatOne($model);
    }

    /**
     * Format a single property value output.
     *
     * @param $model
     *
     * @return mixed
     */
    protected function formatOne($model)
    {
        return $model->value;
    }

    /**
     * Will loop a collection of values and format every single
     * value item.
     *
     * @param $models
     *
     * @return Collection
     */
    protected function formatMany($models)
    {
        $formattedCollection = new Collection();

        foreach ($models as $model)
        {
            $formattedCollection->put(
                $model->id, $this->formatOne($model)
            );
        }

        return $formattedCollection;
    }
}