<?php

$factory->define(Company::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company
    ];
});

$factory->define(\Devio\Eavquent\Value\VarcharValue::class, function (Faker\Generator $faker) {
    return [];
});