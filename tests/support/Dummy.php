<?php

use Devio\Eavquent\Value\Data\Varchar;
use Devio\Eavquent\Attribute\Attribute;

class Dummy
{
    public static function createDummyData()
    {
        $faker = Faker\Factory::create();

        // Simple attribute with values
        $cityAttribute = Attribute::create([
            'name'          => 'city',
            'label'         => 'City',
            'model'         => Varchar::class,
            'entity'        => Company::class,
            'default_value' => null
        ]);

        // Collection attribute with values
        $colorsAttribute = Attribute::create([
            'name'          => 'colors',
            'label'         => 'Colors',
            'model'         => Varchar::class,
            'entity'        => Company::class,
            'default_value' => null,
            'collection'    => true
        ]);

        // Simple attribute without any value
        $addressAttribute = Attribute::create([
            'name'          => 'address',
            'label'         => 'Address',
            'model'         => Varchar::class,
            'entity'        => Company::class,
            'default_value' => null
        ]);

        // Collection attribute without any value
        $sizesAttribute = Attribute::create([
            'name'          => 'sizes',
            'label'         => 'Sizes',
            'model'         => Varchar::class,
            'entity'        => Company::class,
            'default_value' => null,
            'collection'    => true
        ]);

        factory(Company::class, 5)->create()->each(function ($item) use ($faker, $cityAttribute, $colorsAttribute) {
            factory(Varchar::class)->create([
                'content'      => $faker->city,
                'attribute_id' => $cityAttribute->id,
                'entity_id'    => $item->getKey()
            ]);

            factory(Varchar::class, 2)->create([
                'content'      => $faker->colorName,
                'attribute_id' => $colorsAttribute->id,
                'entity_id'    => $item->getKey()
            ]);
        });
    }
}
