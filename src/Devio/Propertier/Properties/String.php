<?php
namespace Devio\Propertier\Properties;

class String extends PropertyAbstract
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