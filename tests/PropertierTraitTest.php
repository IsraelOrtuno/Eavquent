<?php

use Illuminate\Support\Collection;

class PropertierTraitTest extends TestCase
{
    public function testPropertiesAreIncludedInFillableProperty()
    {
        $this->assertTrue($this->company->isFillable('country'));
        $this->assertTrue($this->company->isFillable('colors'));
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
        $this->assertTrue($this->company->isProperty('option'));
        $this->assertTrue($this->company->isProperty('country'));

        $this->assertFalse($this->company->isProperty('state'));
        $this->assertFalse($this->company->isProperty('region'));
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
        $this->assertInstanceOf(Collection::class, $this->company->employees);
    }
}