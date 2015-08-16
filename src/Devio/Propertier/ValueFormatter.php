<?php namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Devio\Propertier\Properties\PropertyFactory;

class ValueFormatter {

    /**
     * @var mixed
     */
    protected $model;

    /**
     * @param Collection $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param Collection $model
     * @return $this
     */
    public static function make($model)
    {
        return new static($model);
    }

    /**
     * Format the property value model set.
     *
     * @return Collection
     */
    public function format()
    {
        if ($this->model instanceof Collection)
        {
            return $this->formatMany($this->model);
        }
        return $this->formatOne($this->model);
    }

    /**
     * Format a single property value output.
     *
     * @param $model
     * @return mixed
     */
    protected function formatOne($model)
    {
        return PropertyFactory::make($model->property)
                              ->value($model)
                              ->decorate($model->value);
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