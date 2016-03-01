<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data Types
    |--------------------------------------------------------------------------
    |
    | Here you may set the value types available for the Eavquent package.
    | You can register a key value pair which will include a plain name
    | formated as studly_case as key and the type class path as value.
    |
    | Feel free to register any new value types into this array. Just make
    | sure you extend Devio\Eavquent\AbstractValue. Once extended use it
    | as any other Eloquent model. Consider using set and get mutators.
    |
    */
    'values' => [
        'varchar' => Devio\Eavquent\Value\VarcharValue::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Prefixes
    |--------------------------------------------------------------------------
    */
    'prefix' => [
        'package_tables' => 'eav_',
        'value_tables' => 'values_'
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key
    |--------------------------------------------------------------------------
    */
    'cache_key' => 'eav',
];