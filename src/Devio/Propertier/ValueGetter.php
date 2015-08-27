<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;

class ValueGetter
{

    /**
     * @var Model
     */
    private $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param Model $model
     * @return static
     */
    public static function make(Model $model)
    {
        return new static($model);
    }

    /**
     * @param $key
     * @return \Illuminate\Support\Collection
     */
    public function obtain($key)
    {
        $property = $this->model->getProperty($key);
        $values = $property->values;

        // If the property is multivalue, it will return a values collection.
        // If it does not just set the value to the unique property value.
        // No matter if one or many, the formatter outputs acordingly.
        if ( ! $property->isMultivalue())
        {
            $values = $values->first();
        }

        return ValueFormatter::make($values)->format();
    }
}