<?php

use Devio\Propertier\Value;
use Devio\Propertier\Property;

$factory->define(Company::class, function ($faker)
{
    return [
        'name' => $faker->company
    ];
});

$factory->define(Employee::class, function ($faker)
{
    return [
        'name' => $faker->name
    ];
});

$factory->define(Property::class, function ()
{
    return [
        'type'   => 'string',
        'entity' => 'Company'
    ];
});

$factory->define(Value::class, function ($faker)
{
    return [
        'value' => $faker->word
    ];
});