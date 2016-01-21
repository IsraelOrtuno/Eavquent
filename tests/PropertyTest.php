<?php

use Devio\Propertier\Value;
use Devio\Propertier\Property;
use Illuminate\Database\Eloquent\Collection;

class PropertyTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Company::setModelColumns(['name']);
    }

    /** @test */
    public function get_value_provides_null_when_no_values_found()
    {
        $property = new Property;

        $this->assertNull($property->getValue());
    }

    /** @test */
    public function it_can_get_a_single_value()
    {
        $property = new Property;
        $property->setRelation('value', new Value(['value' => 'foo']));

        $value = $property->getValue();

        $this->assertEquals('foo', $value);
    }

    /** @test */
    public function it_can_get_a_multivalue()
    {
        $property = new Property(['multivalue' => true]);
        $property->setRelation('values', new Collection([
            new Value(['value' => 'foo']),
            new Value(['value' => 'bar'])
        ]));

        $value = $property->getValue();

        $this->assertInstanceOf(Collection::class, $value);
        $this->assertEquals('foo', $value->first());
        $this->assertEquals('bar', $value->last());
    }

    /** @test */
    public function it_can_replicate_an_existing_property()
    {
        $property = new Property;
        $property->exists = true;

        $result = $property->replicateExisting();

        $this->assertEquals($result, $property);
        $this->assertTrue($result->exists);
    }
}