<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = ['type', 'name', 'multivalue', 'entity', 'default_value'];

    /**
     * The property values relationship.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->hasMany(Value::class);
    }

    /**
     * Replicates a model and set it as existing.
     *
     * @return mixed
     */
    public function replicateExisting()
    {
        $instance = parent::replicate(['*']);
        $instance->exists = $this->exists;

        return $instance;
    }

    /**
     * Get the property value as single value or collection of values.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($values = $this->values)) {
            return $values;
        }

        // Will return null if there is no value for the property. If the property
        // is registered as a multi values property, we will return a collection
        // of values, otherwise we can return the plain object content instead.
        return $this->isMultivalue()
            ? $values->pluck('value')
            : $values->getAttribute('value');
    }

    /**
     * Set the value to the given value.
     *
     * @param Value $value
     * @return $this
     */
    public function setValue(Value $value)
    {
        return $this->setRelation('values', $value);
    }

    /**
     * Pushes a new value into the values collection.
     *
     * @param Value $value
     * @return $this
     */
    public function pushValue(Value $value)
    {
        $this->initializeValues();

        $this->values->push($value);

        return $this;
    }

    /**
     * Pushes a new value into the values relationship collection.
     *
     * @param $value
     * @return mixed
     */
    public function setOrPushValue($value)
    {
        return $this->isMultivalue()
            ? $this->pushValue($value)
            : $this->setValue($value);
    }

    /**
     * Initialize the values relation to an empty collection.
     *
     * @return $this
     */
    protected function initializeValues()
    {
        if (is_null($this->values)) {
            $this->setRelation('values', new Collection);
        }

        return $this;
    }

    /**
     * Check if the property accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return $this->getAttribute('multivalue');
    }
}
