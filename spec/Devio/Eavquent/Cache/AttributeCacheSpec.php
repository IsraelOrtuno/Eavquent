<?php

namespace spec\Devio\Eavquent\Cache;

use Illuminate\Support\Collection;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Illuminate\Contracts\Cache\Repository;

class AttributeCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Cache\AttributeCache');
    }

    function let(Repository $cache)
    {
        $this->beConstructedWith($cache);
    }

    function it_should_check_if_cache_exists(Repository $cache)
    {
        $cache->has('eav')->shouldBeCalled()->willReturn(false);
        $this->exists()->shouldBe(false);

        $cache->has('eav')->shouldBeCalled()->willReturn(true);
        $this->exists()->shouldBe(true);
    }

    function it_should_get_all_attributes(Repository $cache)
    {
        $collection = new Collection(['foo', 'bar']);
        $cache->get('eav')->shouldBeCalled()->willReturn($collection);

        $this->get()->shouldBe($collection);
    }

    function it_should_store_given_attributes(Repository $cache)
    {
        $collection = new Collection;

        $cache->forget('eav')->shouldBeCalled();
        $cache->forever('eav', $collection)->shouldBeCalled();

        $this->set($collection);
    }
}
