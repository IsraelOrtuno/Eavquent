<?php
namespace Devio\Propertier\Properties;

class IntegerProperty extends PropertyAbstract
{
    /**
     * Casting to integer before getting.
     *
     * @param $value
     *
     * @return int
     */
    public function getValueAttribute($value)
    {
        return (int) $value;
    }
}