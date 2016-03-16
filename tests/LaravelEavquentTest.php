<?php

use Devio\Eavquent\Value\VarcharValue;
use Devio\Eavquent\Attribute\Attribute;

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

        $this->assertTrue($company->relationLoaded('colors'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function eagerload_all_attributes_from_withs_model_property()
    {
        $model = CompanyWithEavStub::first();

        $this->assertTrue($model->relationLoaded('colors'));
        $this->assertTrue($model->relationLoaded('city'));
    }

    /** @test */
    public function eagerload_attributes_from_withs_model_property()
    {
        $model = CompanyWithCityStub::first();

        $this->assertFalse($model->relationLoaded('colors'));
        $this->assertTrue($model->relationLoaded('city'));
    }

    /** @test */
    public function load_only_certain_attributes_for_an_entity()
    {
        $company = Company::with('city')->first();

        $this->assertFalse($company->relationLoaded('colors'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function get_the_content_of_an_attribute()
    {
        $company = Company::with('eav')->first();

        $this->assertInternalType('string', $company->city);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $company->colors);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $company->sizes);
        $this->assertNull($company->address);
        $this->assertCount(0, $company->sizes);
    }

    /** @test */
    public function get_the_raw_relation_value()
    {
        $company = Company::with('eav')->first();

        $this->assertInstanceOf(VarcharValue::class, $company->rawCityObject);
    }

    /** @test */
    public function attributes_are_included_in_array_as_keys()
    {
        $company = Company::with('eav')->first()->toArray();

        $this->assertArrayHasKey('city', $company);
        $this->assertArrayHasKey('colors', $company);
        $this->assertArrayHasKey('address', $company);
        $this->assertArrayHasKey('sizes', $company);
    }

    public function createDummyData()
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

class CompanyWithEavStub extends Company
{
    public $table = 'companies';
    public $morphClass = 'Company';

    protected $with = ['eav'];
}

class CompanyWithCityStub extends Company
{
    public $table = 'companies';
    public $morphClass = 'Company';

    protected $with = ['city'];
}