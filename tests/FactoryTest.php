<?php

use Devio\Propertier\Factory;
use Devio\Propertier\Property;
use Devio\Propertier\Values\StringValue;
use Devio\Propertier\Values\IntegerValue;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        Factory::register([
            'integer' => IntegerValue::class,
            'string' => StringValue::class
        ]);
        $this->factory = new Factory();
    }

    /** @test */
    public function it_resolves_a_property_by_model_instance()
    {
        $property = new Property(['type' => 'string']);
        $this->assertEquals(StringValue::class, $this->factory->property($property));
    }

    /** @test */
    public function it_resolves_a_property_by_string()
    {
        $this->assertEquals(StringValue::class, $this->factory->property('string'));
        $this->assertEquals(IntegerValue::class, $this->factory->property('integer'));
    }

    /** @test */
    public function it_throws_exception_if_no_property_found()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->factory->property('foo');
    }

}