<?php
namespace Devio\Propertier\Properties;

class IntegerProperty extends PropertyAbstract
{
    /**
     * Casting the item before accessing.
     *
     * @param $plainValue
     *
     * @return int
     */
    protected function cast($plainValue)
    {
        return (int) $plainValue;
    }

    /**
     * Validating it is a valid integer.
     *
     * @return bool
     */
    public function isValidForStorage()
    {
        return is_integer($this->plainValue);
    }
}