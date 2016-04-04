<?php

use Devio\Eavquent\Value\Data\Varchar;

trait EavquentTestTrait
{
    public function setUp()
    {
        parent::setUp();

        Dummy::createDummyData();
    }

    /** @test */
    public function collections_are_linked_to_entity_and_attribute_when_lazy_load()
    {
        $company = Company::first();

        $this->assertEquals('colors', $company->colors->getAttribute()->code);
        $this->assertEquals($company, $company->colors->getEntity());
    }
    
    /** @test */
    public function collections_are_linked_to_entity_and_attribute_when_eager_load()
    {
        $company = Company::with('eav')->first();

        $this->assertEquals('colors', $company->colors->getAttribute()->code);
        $this->assertEquals($company, $company->colors->getEntity());
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

        $this->assertInstanceOf(Varchar::class, $company->rawCityObject);
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

    /** @test */
    public function value_collections_are_eavquent_collections()
    {
        $company = Company::with('eav')->first();

        $this->assertInstanceOf(\Devio\Eavquent\Value\Collection::class, $company->rawColorsObject);
    }
    
    /** @test */
    public function collections_are_linked_to_entity_and_attribute()
    {
        $company = Company::with('eav')->first();

        $attribute = $company->getEntityAttributes()['colors'];

        $this->assertEquals($company, $company->colors->getEntity());
        $this->assertEquals($attribute, $company->colors->getAttribute());
    }

    /** @test */
    public function updating_content_of_existing_simple_values()
    {
        $company = Company::with('eav')->first();
        $company->city = 'foo';

        $this->assertEquals('foo', $company->city);
        $this->assertEquals(1, $company->rawCityObject->getKey());
    }

    /** @test */
    public function setting_content_of_unexisting_simple_value()
    {
        $company = Company::with('eav')->first();

        $this->assertNull($company->rawAddressObject);
        $company->address = 'foo';

        $value = $company->rawAddressObject;

        $this->assertEquals('foo', $company->address);
        $this->assertNull($value->getKey());
        $this->assertInstanceOf(Varchar::class, $value);
    }

    /** @test */
    public function replacing_content_of_existing_collection_value()
    {
        $company = Company::with('eav')->first();

        $company->colors = ['foo', 'bar'];

        $this->assertCount(2, $company->colors);
        $this->assertCount(1, $company->colors->where('content', 'foo'));
        $this->assertCount(1, $company->colors->where('content', 'bar'));
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
