<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    |
    | Here you may set the type of properties available for the Propertier
    | package. You can register a key value pair which will include the
    | property plain name as key and the property class path as value.
    |
    */
    'properties' => [
        'integer'  => Devio\Propertier\Properties\Integer::class,
        'choice'   => Devio\Propertier\Properties\Choice::class,
        'datetime' => Devio\Propertier\Properties\Datetime::class,
        'string'   => Devio\Propertier\Properties\String::class,
    ]

];