<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\Eavquent;
use Devio\Eavquent\Interactor;
use Devio\Eavquent\Attribute\Manager;
use Illuminate\Contracts\Container\Container;

class EavquentSpec extends ObjectBehavior
{
    function let(Container $container, Manager $manager, Interactor $interactor)
    {
        $this->beAnInstanceOf(EavquentStub::class);
        $container->make(Manager::class)->willReturn($manager);
        $container->make(Interactor::class, [$this])->willReturn($interactor);
        $this->setContainer($container);
    }

    function it_should_resolve_attribute_manager_from_container()
    {
        $this->getAttributeManager()->shouldBeAnInstanceOf(Manager::class);
    }

    function it_should_read_an_attribute(Interactor $interactor)
    {
        $interactor->isAttribute('foo')->shouldBeCalled()->willReturn(true);
        $interactor->get('foo')->shouldBeCalled()->willReturn('bar');

        $this->getAttribute('foo')->shouldBe('bar');
    }
}

class EavquentStub
{
    use Eavquent;
}