<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\EntityAttributeValues;
use Illuminate\Contracts\Container\Container;

class EntityAttributeValuesSpec extends ObjectBehavior
{
    function let(Container $container, Manager $manager)
    {
        $this->beAnInstanceOf(EntityAttributeValuesStub::class);
        $container->make(Manager::class)->willReturn($manager);
        $this->setContainer($container);
    }

    function it_should_resolve_attribute_manager_from_container()
    {
        $this->createAttributeManager();

        $this->attributeManager->shouldBeAnInstanceOf(Manager::class);
    }
}

class EntityAttributeValuesStub
{
    use EntityAttributeValues;
}