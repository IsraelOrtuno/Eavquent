<?php namespace Devio\Propertier\Properties;

class String extends PropertyAbstract {

    public function decorate($value)
    {
        return (string) $value;
    }

    protected function cast($plainValue)
    {
        return (string) $plainValue;
    }
}