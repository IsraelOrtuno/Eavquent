<?php

use Devio\Propertier\Models\Property;
use Illuminate\Support\Collection;

class PropertierTraitTest extends TestCase
{

    protected $company;

    public function setUp()
    {
        parent::setUp();

        $this->registerProperties();
        $this->registerCompany();
    }

    public function testPropertyIsSavedEvenIfModelIsFresh()
    {
        $company = factory(Company::class)->create();
        $company->country = 'foo';
        $company->save();

        $companyItem = Company::find($company->id);

        $this->assertEquals($companyItem->country, 'foo');
    }
    
    public function testPropertyCanBeSetAndReadBeforeSaving()
    {
        $company = factory(Company::class)->create();

        $company->country = 'foo';

        $this->assertEquals($company->country, 'foo');
        $this->assertEquals($company->getProperty('country'), 'foo');
        $this->assertEquals($company->getProperty('country'), $company->country);
    }

    public function testPropertyAttributesAreIdentificable()
    {
        $this->assertTrue($this->company->isProperty('option'));
        $this->assertTrue($this->company->isProperty('country'));

        $this->assertFalse($this->company->isProperty('state'));
        $this->assertFalse($this->company->isProperty('region'));
    }

    public function testPropertiesDoNotInterfiereIfMatchingColumnName()
    {
        $company = factory(Company::class)->create(['name' => 'Devio']);

        $this->assertEquals($company->getAttribute('name'), 'Devio');
        $this->assertEquals($company->name, 'Devio');
        $this->assertEquals($company->name, $company->getAttribute('name'));
    }

    public function testPropertiesDoNotInterfiereIfMatchingRelationName()
    {
        $this->assertInstanceOf(Collection::class, $this->company->employees);
    }

    protected function registerProperties()
    {
        factory(Property::class)->create([
            'type' => 'integer',
            'name' => 'option'
        ]);

        factory(Property::class)->create([
            'type' => 'string',
            'name' => 'country'
        ]);

        factory(Property::class)->create([
            'type' => 'string',
            'name' => 'name'
        ]);

        factory(Property::class)->create([
            'type' => 'string',
            'name' => 'employees'
        ]);
    }

    protected function registerCompany()
    {
        $this->company = factory(Company::class)->create();
    }

}