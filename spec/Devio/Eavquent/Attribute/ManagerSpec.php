<?php

namespace spec\Devio\Eavquent\Attribute;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Illuminate\Support\Collection;
use Devio\Eavquent\AttributeCache;
use Devio\Eavquent\Attribute\Repository;

class ManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Attribute\Manager');
    }

    function let(AttributeCache $cache, Repository $repository)
    {
        $this->beConstructedWith($cache, $repository);
    }

    function it_should_get_attributes(AttributeCache $cache)
    {
        $cache->exists()->shouldBeCalled()->willReturn(true);
        $cache->get()->shouldBeCalled();
        $this->get();
    }

    function it_should_refresh_if_cache_is_unset(AttributeCache $cache, Repository $repository)
    {
        $cache->exists()->shouldBeCalled()->willReturn(false);
        $this->it_should_refresh_attributes_cache($cache, $repository);
        $cache->get()->shouldBeCalled();

        $this->get();
    }

    function it_should_refresh_attributes_cache(AttributeCache $cache, Repository $repository)
    {
        $collection = new Collection();

        $repository->all()->shouldBeCalled()->willReturn($collection);
        $cache->set($collection)->shouldBeCalled();

        $this->refresh();
    }
}
