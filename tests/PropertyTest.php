<?php

use Devio\Propertier\Value;
use Devio\Propertier\Property;
use Illuminate\Support\Collection;

class PropertyTest extends TestCase
{
    /** @test */
    public function get_value_provides_null_when_no_values_found()
    {
        $property = factory(Property::class)->make();

        $this->assertNull($property->getValue());
    }

    /** @test */
    public function get_value_provides_a_plain_value_when_not_multivalue()
    {
        $property = factory(Property::class)->make();
        $property->setRelation('values', new Value(['value' => 'foo']));

        $value = $property->getValue();

        $this->assertEquals('foo', $value);
    }

    /** @test */
    public function get_value_provides_an_array_when_multivalue()
    {
        $property = factory(Property::class)->make(['multivalue' => true]);
        $property->setRelation('values', collect([
            ['value' => 'foo'], ['value' => 'bar']
        ]));

        $value = $property->getValue();

        $this->assertInstanceOf(Collection::class, $value);
        $this->assertEquals('foo', $value->first());
        $this->assertEquals('bar', $value->last());
    }

    /** @test */
    public function it_can_replicate_an_existing_property()
    {
        $property = factory(Property::class)->make();
        $property->exists = true;

        $result = $property->replicateExisting();

        $this->assertEquals($result, $property);
        $this->assertTrue($result->exists);
    }
}