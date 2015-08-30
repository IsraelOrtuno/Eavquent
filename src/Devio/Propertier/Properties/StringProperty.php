<?php
namespace Devio\Propertier\Properties;

class StringProperty extends PropertyAbstract
{

    public function decorate()
    {
        return (string) $this->plainValue;
    }

    protected function cast($plainValue)
    {
        return (string) $plainValue;
    }
}