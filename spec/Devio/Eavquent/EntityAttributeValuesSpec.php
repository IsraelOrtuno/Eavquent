<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\ReadQuery;
use Devio\Eavquent\WriteQuery;
use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\EntityAttributeValues;
use Illuminate\Contracts\Container\Container;

class EntityAttributeValuesSpec extends ObjectBehavior
{
    function let(Container $container, Manager $manager, ReadQuery $readQuery, WriteQuery $writeQuery)
    {
        $this->beAnInstanceOf(EntityAttributeValuesStub::class);
        $container->make(Manager::class)->willReturn($manager);
        $container->make(ReadQuery::class, [$this])->willReturn($readQuery);
        $container->make(WriteQuery::class, [$this])->willReturn($writeQuery);
        $this->setContainer($container);
    }

    function it_should_resolve_attribute_manager_from_container()
    {
        $this->getAttributeManager()->shouldBeAnInstanceOf(Manager::class);
    }

    function it_should_read_an_attribute(ReadQuery $readQuery)
    {
        $readQuery->isAttribute('foo')->shouldBeCalled()->willReturn(true);
        $readQuery->read('foo')->shouldBeCalled()->willReturn('bar');

        $this->getAttribute('foo')->shouldBe('bar');
    }
}

class EntityAttributeValuesStub
{
    use EntityAttributeValues;
}