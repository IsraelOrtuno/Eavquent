<?php

use Mockery as m;
use Devio\Propertier\Value;
use Devio\Propertier\Factory;
use Devio\Propertier\Property;
use Devio\Propertier\Values\StringValue;
use Devio\Propertier\Values\IntegerValue;

class ValueTest extends PHPUnit_Framework_TestCase
{
    public function tearDown() {
        m::close();
    }

    /** @test */
    public function it_casts_a_value_to_a_given_value_type_object()
    {
        $factory = m::mock(Factory::class);
        $value = new Value();
        $value->setFactory($factory);
        $property = new Property(['type' => 'integer']);

        $factory->shouldReceive('property')->twice()->andReturn(IntegerValue::class);

        $this->assertInstanceOf(IntegerValue::class, $value->castObjectTo($property));
        $this->assertEquals($value->getAttributes(), $value->castObjectTo($property)->getAttributes());
    }

    /** @test */
    public function it_does_try_to_cast_a_casted_value()
    {
        $value = new StringValue();
        $property = new Property(['type' => 'integer']);

        $this->assertInstanceOf(StringValue::class, $value->castObjectTo($property));
    }
}