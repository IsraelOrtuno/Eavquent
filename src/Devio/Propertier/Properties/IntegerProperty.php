<?php
namespace Devio\Propertier\Properties;

class IntegerProperty extends PropertyAbstract
{

    protected function cast($plainValue)
    {
        return (int) $plainValue;
    }

    public function isValidForStorage()
    {
        return is_integer($this->plainValue);
    }
}