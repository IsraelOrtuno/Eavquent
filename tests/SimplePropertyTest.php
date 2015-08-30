<?php

class SimplePropertyTest extends Testcase
{
    public function testPropertyMayBeSetIfModelAlreadyExists()
    {
        $company = factory(Company::class)->create();
        $company->country = 'bar';
        $company->save();

        $companyItem = Company::find($company->id);

        $this->assertEquals($companyItem->country, 'bar');
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
    
}