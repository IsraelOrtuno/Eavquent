<?php
namespace Devio\Propertier\Services;

use Illuminate\Database\Eloquent\Model;

class ValueGetter
{
    /**
     * The value formatter instance.
     *
     * @var ValueFormatter
     */
    protected $formatter;

    /**
     * Creates a new ValueGetter instance.
     */
    public function __construct()
    {
        $this->formatter = new ValueFormatter();
    }

    /**
     * Will return the entity property value if any.
     *
     * @param Model $model
     * @param $key
     * @return \Illuminate\Support\Collection
     */
    public function obtain(Model $model, $key)
    {
        $property = $model->getPropertyObject($key);
        $values = $property->values;

        // If the property is multivalue, it will return a values collection.
        // If it does not just set the value to the unique property value.
        // No matter if one or many, the formatter outputs acordingly.
        if ( ! $property->isMultivalue())
        {
            $values = $values->count() ? $values->first() : null;
        }

        return $this->formatter->format($values);
    }
}