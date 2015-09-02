<?php

use Illuminate\Support\Collection;

class PropertierTraitTest extends TestCase
{
    public function testPropertiesAreIncludedInFillableProperty()
    {
        $this->assertTrue($this->company->isFillable('country'));
    }

    public function testPropertyNamesCanBeGet()
    {
        $properties = ['option', 'country', 'name', 'employees', 'colors'];

        $this->assertEquals(
            $this->company->getPropertyNames(true),
            $properties
        );
    }

    public function testPropertyAttributesAreIdentificable()
    {
        $company = factory(Company::class)->create();

        $this->assertTrue($company->isProperty('option'));
        $this->assertTrue($company->isProperty('country'));

        $this->assertFalse($company->isProperty('state'));
        $this->assertFalse($company->isProperty('region'));
    }

    public function testPropertiesDoNotInterfiereIfMatchingColumnName()
    {
        $company = factory(Company::class)->create(['name' => 'foo bar']);

        $this->assertEquals($company->getAttribute('name'), 'foo bar');
        $this->assertEquals($company->name, 'foo bar');
        $this->assertEquals($company->name, $company->getAttribute('name'));
    }

    public function testPropertiesDoNotInterfiereIfMatchingRelationName()
    {
        $company = factory(Company::class)->create();

        $this->assertInstanceOf(Collection::class, $company->employees);
    }
}