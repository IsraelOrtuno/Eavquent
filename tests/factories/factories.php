<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

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

$factory->define(Property::class, function ($faker)
{
    return [
        'type' => 'string',
        'entity' => 'Company'
    ];
});