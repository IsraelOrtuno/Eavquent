<?php

namespace spec\Devio\Propertier;

use RuntimeException;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Propertier\Value;
use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PropertierQuerySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\PropertierQuery');
    }

    function let(Entity $entity)
    {
        $this->beConstructedWith($entity);
    }

    public function it_should_get_the_value_of_a_property(Entity $entity)
    {
        $properties = new Collection(['city' => new PropertyStub]);

        $entity->getRelationValue('properties')->shouldBeCalled()->willReturn($properties);

        $this->getValue('city')->shouldReturn('foo');
        $this->getValueObject('city')->shouldReturnAnInstanceOf(Value::class);
        $this->shouldThrow(RuntimeException::class)->during('getValue', ['color']);
        $this->shouldThrow(RuntimeException::class)->during('getValueObject', ['color']);
    }

    public function it_should_get_a_property(Entity $entity)
    {
        $properties = new Collection(['city' => 'property']);
        $entity->getRelationValue('properties')->shouldBeCalled()->willReturn($properties);

        $this->getProperty('integer')->shouldReturn(null);
        $this->getProperty('city')->shouldReturn('property');
    }

    public function it_should_check_if_a_property_exists(Entity $entity)
    {
        Entity::setModelColumns(['name']);
        $entity->getRelationValue('city')->shouldBeCalled()->willReturn(false);
        $entity->getRelationValue('color')->shouldBeCalled()->willReturn(false);
        $entity->getKeyName()->shouldBeCalled()->willReturn('id');
        $entity->getRelationValue('properties')->shouldBeCalled()
            ->willReturn(new Collection(['city' => 'foo']));

        $this->isProperty('city')->shouldReturn(true);
        $this->isProperty('color')->shouldReturn(false);
    }

    public function it_should_not_recognize_a_relation_as_property(Entity $entity)
    {
        Entity::setModelColumns(['name']);
        $entity->getRelationValue('employees')->shouldBeCalled()->willReturn(true);

        $this->isProperty('employees')->shouldReturn(false);
    }

    public function it_should_not_recognize_an_attribute_as_property()
    {
        Entity::setModelColumns(['name']);

        $this->isProperty('name')->shouldReturn(false);
    }

    public function it_should_not_recognize_pk_as_property(Entity $entity)
    {
        Entity::setModelColumns(['name']);
        $entity->getRelationValue('id')->shouldBeCalled()->willReturn(false);
        $entity->getKeyName()->shouldBeCalled()->willReturn('id');

        $this->isProperty('id')->shouldReturn(false);
    }
}

class Entity extends Model
{
    use Propertier;
}

class PropertyStub
{
    public function getValue()
    {
        return 'foo';
    }

    public function getValueObject()
    {
        return new Value;
    }
}