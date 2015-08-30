<?php
namespace Devio\Propertier\Properties;

class BooleanProperty extends PropertyAbstract
{

    protected function cast($plainValue)
    {
        return (bool) $plainValue;
    }

    public function isValidForStorage()
    {
        return is_bool($this->plainValue);
    }
}