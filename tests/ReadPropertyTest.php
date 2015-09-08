<?php

use Devio\Propertier\PropertyValue;

class ReadPropertyTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpProperties();

        factory(PropertyValue::class)->create([
            'value'       => 'bar',
            'property_id' => 1,
            'entity_type' => 'Company',
            'entity_id'   => $this->company->id
        ]);
    }

    public function test_read_non_existing_value()
    {
        $company = $this->company;

        $this->assertNull($company->getPropertyRawValue('bar'));
    }
    
    public function test_read_existing_value()
    {
        $company = $this->company;

        $value = $company->getPropertyRawValue('foo');

        $this->assertEquals($value->value, 'bar');
    }
}