<?php

$factory->define(Company::class, function (Faker\Generator $faker)
{
    return [
        'name' => $faker->company
    ];
});