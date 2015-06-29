<?php namespace Devio\Propertier\Properties;

class Integer extends PropertyAbstract {

    protected function cast($plainValue)
    {
        return (int) $plainValue;
    }

    public function decorate($value)
    {
        return (int) $value;
    }

    public function isValidForStorage()
    {
        return is_integer($this->plainValue);
    }
}