<?php

use Devio\Propertier\Property;
use Devio\Propertier\Resolver;
use Devio\Propertier\Values\StringValue;
use Devio\Propertier\Values\IntegerValue;

class ResolverTest extends PHPUnit_Framework_TestCase
{
    protected $resolver;

    public function setUp()
    {
        Resolver::register([
            'integer' => IntegerValue::class,
            'string' => StringValue::class
        ]);
        $this->resolver = new Resolver;
    }

    /** @test */
    public function it_resolves_a_property_by_model_instance()
    {
        $property = new Property(['type' => 'string']);
        $this->assertInstanceOf(StringValue::class, $this->resolver->value($property));
    }

    /** @test */
    public function it_resolves_a_property_by_string()
    {
        $this->assertInstanceOf(StringValue::class, $this->resolver->value('string'));
        $this->assertInstanceOf(IntegerValue::class, $this->resolver->value('integer'));
    }

    /** @test */
    public function it_throws_exception_if_no_property_found()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->resolver->value('foo');
    }

    /** @test */
    public function it_resolves_a_property_with_attributes()
    {
        $propertyValue = $this->resolver->value('string', ['value' => 'foo']);
        $this->assertEquals(['value' => 'foo'], $propertyValue->toArray());
    }
}