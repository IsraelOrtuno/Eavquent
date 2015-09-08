<?php
namespace Devio\Propertier\Facades;

use Illuminate\Support\Facades\Facade;

class Propertier extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'propertier';
    }
    
}