<?php

class SimplePropertyTest extends Testcase
{
    public function testPropertyMayBeSetIfModelAlreadyExists()
    {
        $this->company->country = 'bar';
        $this->company->save();

        $companyItem = Company::find($this->company->id);

        $this->assertEquals($companyItem->country, 'bar');
    }

    public function testPropertyIsSavedEvenIfModelIsFresh()
    {
        $company = factory(Company::class)->make();
        $company->country = 'foo';
        $company->save();

        $companyItem = Company::find($company->id);

        $this->assertEquals($companyItem->country, 'foo');
    }

    public function testPropertyCanBeSetAndReadBeforeSaving()
    {
        $company = factory(Company::class)->make();

        $company->country = 'foo';
        $this->company->country = 'bar';

        $this->assertEquals($company->country, 'foo');
        $this->assertEquals($this->company->country, 'bar');

        $this->assertEquals($company->getProperty('country'), 'foo');
        $this->assertEquals($this->company->getProperty('country'), 'bar');

        $this->assertEquals($company->getProperty('country'), $company->country);
        $this->assertEquals($this->company->getProperty('country'), $this->company->country);
    }
    
}