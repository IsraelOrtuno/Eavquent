<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value as PropertyValue;

class IntegerValue extends PropertyValue
{
    /**
     * Casting to integer before getting.
     *
     * @param $value
     * @return int
     */
    public function getValueAttribute($value)
    {
        return (int)$value;
    }
}
