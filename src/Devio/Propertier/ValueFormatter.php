<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Devio\Propertier\Models\Property;
use Devio\Propertier\Properties\Factory as PropertyFactory;

class ValueFormatter
{
    /**
     * The property factory instance.
     *
     * @var PropertyFactory
     */
    protected $property;

    /**
     * Creates a ValueFormatter instance.
     *
     * @param $model
     * @param PropertyFactory $property
     */
    public function __construct(PropertyFactory $property)
    {
        $this->property = $property;
    }

    /**
     * Format the property value model set.
     *
     * @return Collection
     */
    public function format($model)
    {
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
     * @return mixed
     */
    protected function formatOne($model)
    {
        return $this->property->make($model->property)
                              ->value($model)
                              ->decorate();
    }

    /**
     * Will loop a collection of values and format every single
     * value item.
     *
     * @param $models
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