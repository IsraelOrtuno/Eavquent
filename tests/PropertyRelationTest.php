<?php

use Devio\Propertier\Property;
use Illuminate\Database\Eloquent\Collection;

class PropertyRelationTest extends TestCase
{
    protected $company;

    protected $employee;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(Company::class)->create();
        $this->employee = factory(Employee::class)->create();

        factory(Property::class)->create(['name' => 'foo']);
        factory(Property::class)->create(['name' => 'bar']);
        factory(Property::class)->create(['name' => 'baz']);
        factory(Property::class)->create([
                'name'   => 'qux',
                'entity' => 'Employee']
        );
        factory(Property::class)->create([
                'name'   => 'quux',
                'entity' => 'Employee']
        );
    }

    public function testEntityMayHaveManyProperties()
    {
        $companyProperties = $this->company->properties->pluck('name');
        $employeeProperties = $this->employee->properties->pluck('name');

        $this->assertCount(3, $companyProperties);
        $this->assertNotFalse($companyProperties->search('foo'));
        $this->assertNotFalse($companyProperties->search('bar'));
        $this->assertNotFalse($companyProperties->search('baz'));
        $this->assertFalse($companyProperties->search('qux'));
        $this->assertFalse($companyProperties->search('quux'));

        $this->assertCount(2, $employeeProperties);
        $this->assertFalse($employeeProperties->search('foo'));
        $this->assertFalse($employeeProperties->search('bar'));
        $this->assertFalse($employeeProperties->search('baz'));
        $this->assertNotFalse($employeeProperties->search('qux'));
        $this->assertNotFalse($employeeProperties->search('quux'));
    }
    
    public function testEntityMayEagerLoadProperties()
    {
        $company = Company::with('properties')->find($this->company->id);

        $this->assertTrue($company->relationLoaded('properties'));
        $this->assertInstanceOf(
            Collection::class, $company->getRelation('properties')
        );
        $this->assertCount(3, $company->getRelation('properties'));
    }
    
}