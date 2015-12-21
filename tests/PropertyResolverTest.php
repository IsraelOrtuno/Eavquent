<?php

use Devio\Propertier\Property;
use Devio\Propertier\PropertyResolver;
use Devio\Propertier\Properties\StringProperty;
use Devio\Propertier\Properties\IntegerProperty;

class PropertyResolverTest extends PHPUnit_Framework_TestCase
{
    protected $resolver;

    public function setUp()
    {
        PropertyResolver::register([
            'integer' => IntegerProperty::class,
            'string' => StringProperty::class
        ]);
        $this->resolver = new PropertyResolver;
    }

    public function test_it_creates_a_property_by_model()
    {
        $property = new Property(['type' => 'string']);
        $this->assertInstanceOf(StringProperty::class, $this->resolver->property($property));
    }

    public function test_it_creates_a_property_by_string()
    {
        $this->assertInstanceOf(StringProperty::class, $this->resolver->property('string'));
        $this->assertInstanceOf(IntegerProperty::class, $this->resolver->property('integer'));
    }

    public function test_it_throws_exception_if_no_property_found()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->resolver->property('foo');
    }

    public function test_it_creates_a_property_with_attributes()
    {
        $propertyValue = $this->resolver->property('string', ['value' => 'foo']);
        $this->assertEquals(['value' => 'foo'], $propertyValue->toArray());
    }
}