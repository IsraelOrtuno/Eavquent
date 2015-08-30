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
        'integer'  => Devio\Propertier\Properties\IntegerProperty::class,
        'choice'   => Devio\Propertier\Properties\ChoiceProperty::class,
        'datetime' => Devio\Propertier\Properties\DatetimeProperty::class,
        'string'   => Devio\Propertier\Properties\StringProperty::class,
    ]

];