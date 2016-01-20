<?php

use Devio\Propertier\Value;
use Devio\Propertier\Property;
use Devio\Propertier\Values\StringValue;

class ValueTest extends TestCase
{
    /** @test */
    public function it_creates_a_new_value_linked_to_property()
    {
        $property = factory(Property::class)->make();
        $value = Value::createInstanceFrom($property, ['value' => 'foo'], true);

        $this->assertTrue($value->exists);
        $this->assertEquals('foo', $value->value);
        $this->assertEquals($property, $value->getProperty());
    }

    /** @test */
    public function it_resolves_a_value_instance_into_value_type_object()
    {
        $entity = factory(Company::class)->make(['id' => 1]);
        $property = factory(Property::class)->make(['id' => 1]);

        $value = Value::resolveValue($property, $entity, 'foo');

        $this->assertInstanceOf(StringValue::class, $value);

        $this->assertNotNull($value->getAttribute('entity_type'));
        $this->assertNotNull($value->getAttribute('entity_id'));
        $this->assertNotNull($value->getAttribute('property_id'));
        $this->assertEquals($property, $value->getProperty());
        $this->assertEquals('foo', $value->getAttribute('value'));
    }
}