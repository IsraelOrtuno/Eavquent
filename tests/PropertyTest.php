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
    public function it_should_add_existing_to_deletion_queue_when_setting_collection()
    {
        $property = new PropertyMultivalueStub;
        $values = $this->getValuesCollection();
        $values->first()->exists = true;
        $values->last()->exists = true;
        $property->setValueRelation($values);

        $property->set('foo');

        $this->assertCount(2, $property->getDeletionQueue());
        $this->assertEquals('utah', $property->getDeletionQueue()->first()->getAttribute('value'));
        $this->assertEquals('sword', $property->getDeletionQueue()->last()->getAttribute('value'));
    }
    
    /** @test */
    public function it_should_replace_values_when_setting_multivalue()
    {
        $property = new PropertyMultivalueStub;
        $property->setValueRelation(new Collection('foo', 'bar'));

        $property->set(['utah', 'omaha']);

        $this->assertEquals(['utah', 'omaha'], $property->get()->toArray());
    }

    /** @test */
    public function it_should_cast_when_setting_values()
    {
        $property = new PropertyStringStub;
        $property->set('foo');

        $this->assertInstanceOf(StringValue::class, $property->getObject());

        $property->setAttribute('multivalue', true);
        $property->set('foo', 'bar');

        $this->assertInstanceOf(StringValue::class, $property->getObject()->first());
        $this->assertInstanceOf(StringValue::class, $property->getObject()->last());
    }

    /** @test */
    public function it_should_set_multivalue_with_func_args()
    {
        $property = new PropertyMultivalueStub;

        $property->set('foo', 'bar');
        $this->assertEquals(['foo', 'bar'], $property->get()->toArray());
    }

    /** @test */
    public function it_should_set_multivalue_with_array()
    {
        $property = new PropertyMultivalueStub;

        $property->set(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $property->get()->toArray());
    }

    /** @test */
    public function it_should_update_existing_value()
    {
        $property = new PropertyStringStub;
        $property->setValueRelation(new StringValue(['value' => 'foo']));

        $this->assertEquals('foo', $property->get());
        $property->set('bar');
        $this->assertEquals('bar', $property->get());
    }

    /** @test */
    public function it_should_set_new_value()
    {
        $property = new PropertyStringStub;
        $property->set('foo');

        $this->assertEquals('foo', $property->get());
    }

    /** @test */
    public function it_should_check_if_property_is_multivalued()
    {
        $property = new Property;
        $this->assertFalse($property->isMultivalue());

        $property->setAttribute('multivalue', true);
        $this->assertTrue($property->isMultivalue());
    }

    /** @test */
    public function it_should_enqueue_current_values_for_deletion()
    {
        $property = new PropertyMultivalueStub;
        $collection = m::mock(Collection::class);
        $collection->shouldReceive('where')->once()->andReturn(['foo', 'bar']);
        $property->setValueRelation($collection);

        $property->enqueueCurrentValues();

        $this->assertEquals(['foo', 'bar'], $property->getDeletionQueue()->toArray());
    }

    /** @test */
    public function it_should_reset_the_values_collection()
    {
        $property = new Property;
        $property->setRelation('values', new Collection(['foo', 'bar']));

        $property->resetValuesRelation();

        $this->assertInstanceOf(Collection::class, $property->getRelationValue('values'));
        $this->assertCount(0, $property->getRelationValue('values'));

    }

    /** @test */
    public function it_should_set_a_values_relation()
    {
        $plainProperty = new Property;
        $multiProperty = new PropertyMultivalueStub;

        $plainProperty->setValueRelation('foo');
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
        $multiProperty = new PropertyMultivalueStub;

        $this->assertEquals('value', $plainProperty->getValueRelationName());
        $this->assertEquals('values', $multiProperty->getValueRelationName());
    }

    /** @test */
    public function it_should_load_a_collection_of_values()
    {
        with($plainProperty = new Property)->setRawAttributes(['id' => 1]);
        with($multiProperty = new PropertyMultivalueStub)->setRawAttributes(['id' => 2]);
        $plainProperty->loadValues($this->getValuesCollection());
        $multiProperty->loadValues($this->getValuesCollection());

        // Checking plain
        $this->assertNull($plainProperty->getRelationValue('values'));
        $this->assertInstanceOf(StringValue::class, $plainProperty->getRelation('value'));
        $this->assertEquals('utah', $plainProperty->get());

        // Checking multi
        $value = $multiProperty->get()->toArray();
        $this->assertNull($multiProperty->getRelationValue('value'));
        $this->assertInstanceOf(Collection::class, $multiProperty->getRelationValue('values'));
        $this->assertCount(2, $value);
        $this->assertTrue(in_array('omaha', $value));
        $this->assertTrue(in_array('gold', $value));
        $this->assertFalse(in_array('utah', $value));
    }

    /** @test */
    public function it_should_cast_values()
    {
        $property = new PropertyStringStub;

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

        $this->assertNull($property->get());
    }

    /** @test */
    public function it_should_get_a_single_value()
    {
        $property = new Property;
        $value = m::mock(Value::class);
        $value->shouldReceive('getAttribute')->once()->andReturn('foo');
        $property->setRelation('value', $value);

        $value = $property->get();

        $this->assertNotInstanceOf(BaseCollection::class, $value);
        $this->assertEquals('foo', $value);
    }

    /** @test */
    public function it_should_get_a_multivalue()
    {
        $property = new PropertyMultivalueStub;
        $value = m::mock(Value::class);
        $value->shouldReceive('getAttribute')->twice()->andReturn('foo', 'bar');
        $property->setRelation('values', new Collection([$value, $value]));

        $value = $property->get();

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

class PropertyStringStub extends Property {
    public function __construct()
    {
        parent::__construct();
        $this->attributes['type'] = 'string';
    }
}

class PropertyMultivalueStub extends PropertyStringStub
{
    public function isMultivalue()
    {
        return true;
    }

    public function getForeignKey()
    {
        return 'property_id';
    }
}