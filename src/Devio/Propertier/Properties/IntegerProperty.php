<?php

namespace Devio\Propertier\Properties;

use Devio\Propertier\PropertyValue;

class IntegerProperty extends PropertyValue
{
    /**
     * Casting to integer before getting.
     *
     * @param $value
     * @return int
     */
    public function getValueAttribute($value)
    {
        return (int) $value;
    }
}
