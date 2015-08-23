<?php
namespace Devio\Propertier\Properties;

use Carbon\Carbon;

class Datetime extends PropertyAbstract
{

    public function cast($plainValue)
    {
        return new Carbon($plainValue);
    }

    public function isValidForStorage()
    {
        return $this->plainValue instanceof Carbon;
    }
}