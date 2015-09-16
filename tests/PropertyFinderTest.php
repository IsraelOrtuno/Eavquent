<?php

use Devio\Propertier\Property;
use Devio\Propertier\PropertyFinder;

class PropertyFinderTest extends PHPUnit_Framework_TestCase
{

    public function test_it_should_return_a_property_that_exists()
    {
        list($finder, $properties) = $this->instances();
        $finder->properties($properties);
        $this->assertEquals(new Property(['name' => 'foo']), $finder->find('foo'));
        $this->assertEquals(new Property(['name' => 'bar']), $finder->find('bar'));
    }

    public function test_it_should_return_a_property_object()
    {
        list($finder, $properties) = $this->instances();
        $finder->properties($properties);
        $this->assertInstanceOf(Property::class, $finder->find('foo'));
    }

    public function test_it_should_return_null_properties_are_empty()
    {
        list($finder) = $this->instances();
        $this->assertNull($finder->find('foo'));
    }

    public function test_it_should_return_null_when_no_property_is_found()
    {
        list($finder, $properties) = $this->instances();
        $finder->properties($properties);
        $this->assertNull($finder->find('baz'));
    }

    public function instances()
    {
        return [
            new PropertyFinder,
            [new Property(['name' => 'foo']), new Property(['name' => 'bar'])]
        ];
    }
    
}