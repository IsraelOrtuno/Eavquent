<?php

namespace spec\Devio\Propertier\Finders;

use Illuminate\Support\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PropertyFinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\Finders\PropertyFinder');
    }

    function it_should_initialize_properties_when_constructing_with_args()
    {
        $items = ['foo', 'bar'];
        $this->beConstructedWith($items);
        $this->getProperties()->shouldBeLike(collect($items));
    }

    function it_should_find_an_existing_property()
    {
        $items = [['name' => 'foo'], ['name' => 'bar']];

        $this->properties($items)
             ->find('foo')
             ->shouldReturn(['name' => 'foo']);
    }

    function it_should_return_false_if_no_property_found()
    {
        $this->properties([])
             ->find('foo')
             ->shouldReturn(false);
    }

    function it_should_accept_arrays()
    {
        $this->properties(['foo', 'bar']);
        $this->getProperties()
             ->shouldBeAnInstanceOf(Collection::class);
    }
}
