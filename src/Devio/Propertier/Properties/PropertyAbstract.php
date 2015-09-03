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
    abstract function setValueAttribute($value);

    /**
     * Casting from database string when getting.
     *
     * @return mixed
     */
    abstract function getValueAttribute($value);

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