<?php

use Devio\Eavquent\Value\VarcharValue;
use Devio\Eavquent\Attribute\Attribute;

class Dummy
{
    public static function createDummyData()
    {
        $faker = Faker\Factory::create();

        // Simple attribute with values
        $cityAttribute = Attribute::create([
            'code'          => 'city',
            'label'         => 'City',
            'model'         => VarcharValue::class,
            'entity'        => Company::class,
            'default_value' => null
        ]);

        // Collection attribute with values
        $colorsAttribute = Attribute::create([
            'code'          => 'colors',
            'label'         => 'Colors',
            'model'         => VarcharValue::class,
            'entity'        => Company::class,
            'default_value' => null,
            'collection'    => true
        ]);

        // Simple attribute without any value
        $addressAttribute = Attribute::create([
            'code'          => 'address',
            'label'         => 'Address',
            'model'         => VarcharValue::class,
            'entity'        => Company::class,
            'default_value' => null
        ]);

        // Collection attribute without any value
        $sizesAttribute = Attribute::create([
            'code'          => 'sizes',
            'label'         => 'Sizes',
            'model'         => VarcharValue::class,
            'entity'        => Company::class,
            'default_value' => null,
            'collection'    => true
        ]);

        factory(Company::class, 5)->create()->each(function ($item) use ($faker, $cityAttribute, $colorsAttribute) {
            factory(VarcharValue::class)->create([
                'content'      => $faker->city,
                'attribute_id' => $cityAttribute->id,
                'entity_type'  => Company::class,
                'entity_id'    => $item->getKey()
            ]);

            factory(VarcharValue::class, 2)->create([
                'content'      => $faker->colorName,
                'attribute_id' => $colorsAttribute->id,
                'entity_type'  => Company::class,
                'entity_id'    => $item->getKey()
            ]);
        });
    }
}