<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    |
    | Here you may set the type of the fields available for the Propertier
    | package. You can register a key value pair which will include the
    | plain field name as key and the value type class path as value.
    |
    | Feel free to register any new field types into this array. Just make
    | sure you extend Devio\Propertier\Value. Once extended use it as a
    | any other Eloquent model. Consider using set and get mutators.
    |
    */
    'fields' => [
        'integer'  => Devio\Propertier\Values\IntegerValue::class,
        'string'   => Devio\Propertier\Values\StringValue::class
    ]

];