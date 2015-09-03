<?php
namespace Devio\Propertier\Properties;

use Devio\Propertier\PropertyAbstract;

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