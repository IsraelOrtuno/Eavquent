<?php
namespace Devio\Propertier\Properties;

class IntegerProperty extends PropertyAbstract
{
    /**
     * Casting before setting.
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->setValue((string) $value);
    }

    /**
     * Casting before getting.
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