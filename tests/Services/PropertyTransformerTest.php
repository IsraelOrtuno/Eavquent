<?php

use Devio\Propertier\Properties\StringProperty;
use Devio\Propertier\Property;
use Devio\Propertier\PropertyValue;
use Devio\Propertier\Services\PropertyTransformer;
use Illuminate\Support\Collection;
use Mockery as m;

class PropertyTransformerTest extends TestCase
{
    
    public function test_transform_one_value()
    {
        list($value, $property) = $this->getMocks();
        $value->shouldReceive('getAttributes')->once()->andReturn([]);
        $property->shouldReceive('getAttribute')->with('type')->once()->andReturn('string');

        $transformer = new PropertyTransformer($value, $property);

        $result = $transformer->transform();

        $this->assertInstanceOf(StringProperty::class, $result);
    }

    public function test_transform_collection()
    {
        list($value, $property) = $this->getMocks();
        $value2 = m::mock(PropertyValue::class);
        $value->shouldReceive('getAttributes')->once()->andReturn([]);
        $value2->shouldReceive('getAttributes')->once()->andReturn([]);
        $property->shouldReceive('getAttribute')->with('type')->twice()->andReturn('string');
        $collection = new Collection([$value, $value2]);
        $transformer = new PropertyTransformer($collection, $property);
        $result = $transformer->transform();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(StringProperty::class, $result->first());
        $this->assertInstanceOf(StringProperty::class, $result->last());
    }

    protected function getMocks()
    {
        return [
            m::mock(PropertyValue::class),
            m::mock(Property::class)
        ];
    }
    
}