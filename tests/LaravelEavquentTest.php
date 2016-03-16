<?php

use Devio\Eavquent\Attribute\Attribute;
use Devio\Eavquent\Value\VarcharValue;

class LaravelEavquentTest extends LaravelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->createDummyData();
    }

    /** @test */
    public function load_all_attributes_registered_for_an_entity()
    {
        $company = Company::with('eav')->first();

        $this->assertTrue($company->relationLoaded('color'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function load_only_certain_attributes_for_an_entity()
    {
        $company = Company::with('city')->first();

        $this->assertFalse($company->relationLoaded('color'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function get_the_content_of_an_attribute()
    {
        $company = Company::with('eav')->first();

        $this->assertInternalType('string', $company->city);
        $this->assertInternalType('string', $company->color);
    }

    /** @test */
    public function get_the_raw_relation_value()
    {
        $company = Company::with('eav')->first();

        $this->assertInstanceOf(VarcharValue::class, $company->rawCityObject);
    }

    public function createDummyData()
    {
        $faker = Faker\Factory::create();

        $cityAttribute = Attribute::create([
            'code' => 'city',
            'label' => 'City',
            'model' => VarcharValue::class,
            'entity' => Company::class,
            'default_value' => null
        ]);

        $colorAttribute = Attribute::create([
            'code' => 'color',
            'label' => 'Color',
            'model' => VarcharValue::class,
            'entity' => Company::class,
            'default_value' => null
        ]);

        factory(Company::class, 5)->create()->each(function ($item) use ($faker, $cityAttribute, $colorAttribute) {
            $varchar = new VarcharValue;
            $varchar->content = $faker->city;
            $varchar->attribute_id = $cityAttribute->id;
            $varchar->entity_type = Company::class;
            $varchar->entity_id = $item->getKey();
            $varchar->save();

            $varchar = new VarcharValue;
            $varchar->content = $faker->colorName;
            $varchar->attribute_id = $colorAttribute->id;
            $varchar->entity_type = Company::class;
            $varchar->entity_id = $item->getKey();
            $varchar->save();
        });
    }
}