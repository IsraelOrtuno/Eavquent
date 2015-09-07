<?php

use Illuminate\Database\Eloquent\Collection;

class PropertyRelationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpProperties();

    }

    public function testPropertiesAreIdentifiable()
    {
        $company = $this->company;
        $employee = $this->employee;

        $this->assertTrue($company->isProperty('foo'));
        $this->assertFalse($company->isProperty('quux'));

        $this->assertTrue($employee->isProperty('qux'));
        $this->assertFalse($employee->isProperty('bar'));

        $this->assertFalse($company->isProperty('properties'));
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

    public function testCheckingPropertiesExistenceWillRunOnlyOneQuery()
    {
        DB::enableQueryLog();

        $company = Company::find($this->company->id);

        $company->isProperty('foo');
        $company->isProperty('bar');
        $company->isProperty('baz');

        // One query for fetching the company and just a query for
        // fetching properties. They will be stored for future checks.
        $this->assertCount(2, DB::getQueryLog());
    }
}