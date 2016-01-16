<?php

use Devio\Propertier\Property;
use Devio\Propertier\PropertyValue;

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

$factory->define(Property::class, function ($faker)
{
    return [
        'type'   => 'string',
        'entity' => 'Company'
    ];
});

$factory->define(PropertyValue::class, function ($faker)
{
    return [
        'value' => $faker->word
    ];
});