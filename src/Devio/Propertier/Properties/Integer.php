<?php namespace Devio\Propertier\Properties;

class Integer extends PropertyAbstract {

    protected function cast($plainValue)
    {
        return (int) $plainValue;
    }

    public function isValidForStorage()
    {
        return is_integer($this->plainValue);
    }
}