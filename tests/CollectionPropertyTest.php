<?php

use Illuminate\Support\Collection;

class CollectionPropertyTest extends TestCase
{
    public function testSettingACollectionOrArray()
    {
        $colors = collect(['foo', 'bar', 'baz']);

        $this->company->colors = $colors;

        $this->assertEquals($this->company->colors, $colors);
        $this->assertEquals($this->company->getProperty('colors'), $colors);

        $this->company->save();

        $company = Company::find($this->company->id);

        $companyColors = $company->colors;

        $this->assertInstanceOf(Collection::class, $companyColors);
        $this->assertEquals(array_values($company->colors->toArray()), ['foo', 'bar', 'baz']);
    }
}