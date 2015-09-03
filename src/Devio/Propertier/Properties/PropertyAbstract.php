<?php
namespace Devio\Propertier\Properties;

use Devio\Propertier\Models\PropertyValue;

abstract class PropertyAbstract extends PropertyValue
{
    /**
     * Casting to database string when setting.
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->setValue((string) $value);
    }

    /**
     * Casting from database string when getting.
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return $value;
    }

    /**
     * Easy setting the value property.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->attributes['value'] = $value;
    }
}