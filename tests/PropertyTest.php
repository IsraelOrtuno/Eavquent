<?php

use Mockery as m;
use Devio\Propertier\Value;
use Devio\Propertier\Property;
use Devio\Propertier\Values\StringValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class PropertyTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Company::setModelColumns(['name']);
    }

    /** @test */
    public function it_should_check_if_property_is_multivalued()
    {
        $property = new Property();
        $this->assertFalse($property->isMultivalue());

        $property->setAttribute('multivalue', true);
        $this->assertTrue($property->isMultivalue());
    }

    /** @test */
    public function it_should_enqueue_current_values_for_deletion()
    {
        $property = new Property(['multivalue' => true]);
        $collection = m::mock(Collection::class);
        $collection->shouldReceive('where')->once()->andReturn(['foo', 'bar']);
        $property->setValueRelation($collection);

        $property->enqueueCurrentValues();

        $this->assertEquals(['foo', 'bar'], $property->getDeletionQueue()->toArray());
    }
    
    /** @test */
    public function it_should_reset_the_values_collection()
    {
        $property = new Property();
        $property->setRelation('values', new Collection(['foo', 'bar']));

        $property->resetValuesRelation();

        $this->assertInstanceOf(Collection::class, $property->getRelationValue('values'));
        $this->assertCount(0, $property->getRelationValue('values'));

    }

    /** @test */
    public function it_should_set_a_values_relation()
    {
        $plainProperty = new Property;
        with($multiProperty = new Property)->setRawAttributes(['multivalue' => true]);

        $plainProperty->setValueRelation(new Collection(['foo']));
        $multiProperty->setValueRelation(new Collection(['foo', 'bar']));

        $this->assertEquals('foo', $plainProperty->getValueRelation());
        $this->assertNull($plainProperty->getRelationValue('values'));

        $this->assertInstanceOf(Collection::class, $multiProperty->getValueRelation());
        $this->assertNull($multiProperty->getRelationValue('value'));
    }

    /** @test */
    public function it_should_fetch_the_value_relation_name()
    {
        $plainProperty = new Property;
        with($multiProperty = new Property)->setRawAttributes(['multivalue' => true]);

        $this->assertEquals('value', $plainProperty->getValueRelationName());
        $this->assertEquals('values', $multiProperty->getValueRelationName());
    }

    /** @test */
    public function it_should_loas_a_collection_of_values()
    {
        with($plainProperty = new Property)->setRawAttributes(['id' => 1]);
        with($multiProperty = new Property)->setRawAttributes(['id' => 2, 'multivalue' => true]);
        $plainProperty->loadValues($this->getValuesCollection());
        $multiProperty->loadValues($this->getValuesCollection());

        // Checking plain
        $this->assertNull($plainProperty->values);
        $this->assertInstanceOf(StringValue::class, $plainProperty->value);
        $this->assertEquals('utah', $plainProperty->getValue());

        // Checking multi
        $value = $multiProperty->getValue()->toArray();
        $this->assertNull($multiProperty->value);
        $this->assertInstanceOf(Collection::class, $multiProperty->values);
        $this->assertCount(2, $value);
        $this->assertTrue(in_array('omaha', $value));
        $this->assertTrue(in_array('gold', $value));
        $this->assertFalse(in_array('utah', $value));
    }

    /** @test */
    public function it_should_cast_values()
    {
        $property = new Property(['type' => 'string']);

        // Single value
        $value = m::mock(Value::class);
        $value->shouldReceive('castObjectTo')->with($property)->once();
        $property->cast($value);

        // Collection of values
        $value = m::mock(Value::class);
        $value->shouldReceive('castObjectTo')->with($property)->twice();
        $values = collect([$value, $value]);

        $property->cast($values);
    }

    /** @test */
    public function it_should_return_null_when_getting_not_found_values()
    {
        $property = new Property;

        $this->assertNull($property->getValue());
    }

    /** @test */
    public function it_should_get_a_single_value()
    {
        $property = new Property;
        $value = m::mock(Value::class);
        $value->shouldReceive('getAttribute')->once()->andReturn('foo');
        $property->setRelation('value', $value);

        $value = $property->getValue();

        $this->assertNotInstanceOf(BaseCollection::class, $value);
        $this->assertEquals('foo', $value);
    }

    /** @test */
    public function it_should_get_a_multivalue()
    {
        $property = new Property(['multivalue' => true]);
        $value = m::mock(Value::class);
        $value->shouldReceive('getAttribute')->twice()->andReturn('foo', 'bar');
        $property->setRelation('values', new Collection([$value, $value]));

        $value = $property->getValue();

        $this->assertInstanceOf(Collection::class, $value);
        $this->assertEquals('foo', $value->first());
        $this->assertEquals('bar', $value->last());
    }

    /** @test */
    public function it_should_replicate_an_existing_property()
    {
        $property = new Property;
        $property->exists = true;

        $result = $property->replicateExisting();

        $this->assertEquals($result, $property);
        $this->assertTrue($result->exists);
    }

    protected function getValuesCollection()
    {
        return new Collection([
            new ValueCastingStub(['property_id' => 1, 'value' => 'utah']),
            new ValueCastingStub(['property_id' => 2, 'value' => 'omaha']),
            new ValueCastingStub(['property_id' => 2, 'value' => 'gold']),
            new ValueCastingStub(['property_id' => 3, 'value' => 'juno']),
            new ValueCastingStub(['property_id' => 3, 'value' => 'sword'])
        ]);
    }
}

class ValueCastingStub extends Value
{
    public function getFactory()
    {
        $mock = m::mock(Factory::class);
        $mock->shouldReceive('property')->once()->andReturn(StringValue::class);
        return $mock;
    }

    protected function isCasted()
    {
        return false;
    }
}