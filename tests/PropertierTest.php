<?php

use Mockery as m;

class PropertierTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpProperties();
    }

    public function test_it_recognizes_an_attribute_is_property_or_not()
    {
        $company = Company::find(1);
//        $this->assertTrue($company->isProperty('foo'));
//        $this->assertTrue($company->isProperty('bar'));
        $this->assertFalse($company->isProperty('oof'));
        $this->assertFalse($company->isProperty('rab'));
    }

    public function test_it_will_get_an_array_of_columns()
    {
        $company = new Company;
        $this->assertInternalType('array', $company->getTableColumns());
    }

    public function test_it_will_cache_the_array_of_columns()
    {
        DB::enableQueryLog();
        $company = new Company;
        $company->getTableColumns();
        $company->getTableColumns();
        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();
    }

    protected function instances()
    {
        return [
            new Company
        ];
    }

}