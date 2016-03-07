<?php

namespace spec\Devio\Eavquent;

use Devio\Eavquent\EavquentManager;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\Eavquent;
use Illuminate\Contracts\Container\Container;
use Devio\Eavquent\Attribute\AttributeManager;

class EavquentSpec extends ObjectBehavior
{
    function let(Container $container, EavquentManager $eavquentManager, AttributeManager $attributeManager)
    {
        $this->beAnInstanceOf(EavquentStub::class);

        $container->make(AttributeManager::class)->willReturn($attributeManager);
        $container->make(EavquentManager::class)->willReturn($eavquentManager);

        $this->setContainer($container);
    }

    function it_should_resolve_attribute_manager_from_container()
    {
        $this->getAttributeManager()->shouldBeAnInstanceOf(AttributeManager::class);
    }
}

class EavquentStub
{
    use Eavquent;
}