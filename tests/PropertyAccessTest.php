<?php

use Devio\Propertier\PropertyValue;

class PropertyAccessTest extends TestCase
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
    
    public function testGetAnExistingPropertyValue()
    {
        $company = $this->company;

        $value = $company->getPropertyRawValue('foo');

        $this->assertEquals($value->value, 'bar');
    }
}