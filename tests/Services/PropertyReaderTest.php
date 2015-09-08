<?php

use Mockery as m;
use Devio\Propertier\Property;
use Devio\Propertier\PropertyValue;
use Devio\Propertier\Services\PropertyFinder;
use Devio\Propertier\Services\PropertyReader;

class PropertyReaderTest extends TestCase
{

    public function test_reading_existing_values()
    {
        $property = new Property(['name' => 'option', 'type' => 'string']);
        $propertier = m::mock(Company::class.'[getValuesOf]');
        $propertier->shouldReceive('getValuesOf')
                   ->with($property)
                   ->once()
                   ->andReturn(collect([new PropertyValue(['value' => 'foo'])]));
        $finder = m::mock(PropertyFinder::class.'[find]');
        $finder->shouldReceive('find')
               ->with('option')
               ->once()
               ->andReturn($property);

        $reader = new PropertyReader($propertier, $finder);

        $value = $reader->read('option');

        $this->assertInstanceOf(PropertyValue::class, $value);
        $this->assertEquals('foo', $value->value);
    }

    public function test_reading_with_empty_values()
    {
        $property = new Property(['name' => 'option', 'type' => 'string']);
        $propertier = m::mock(Company::class.'[getValuesOf]');
        $propertier->shouldReceive('getValuesOf')
                   ->with($property)
                   ->once()
                   ->andReturn(collect());
        $finder = m::mock(PropertyFinder::class.'[find]');
        $finder->shouldReceive('find')
               ->with('option')
               ->once()
               ->andReturn($property);

        $reader = new PropertyReader($propertier, $finder);

        $value = $reader->read('option');

        $this->assertNull($value);
    }

    public function test_reading_property_without_values()
    {
        $property = new Property(['name' => 'option', 'type' => 'string']);
        $propertier = m::mock(Company::class.'[getValuesOf]');
        $propertier->shouldReceive('getValuesOf')
                   ->with($property)
                   ->once()
                   ->andReturn(collect([new PropertyValue(['value' => 'foo'])]));
        $finder = m::mock(PropertyFinder::class.'[find]');
        $finder->shouldReceive('find')
               ->with('option')
               ->once()
               ->andReturn($property);

        $reader = new PropertyReader($propertier, $finder);

        $value = $reader->read('option');

        $this->assertInstanceOf(PropertyValue::class, $value);
        $this->assertEquals('foo', $value->value);
    }
}