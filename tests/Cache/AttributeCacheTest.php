<?php

use Illuminate\Support\Collection;
use Mockery as m;
use Devio\Eavquent\Cache\AttributeCache;
use Illuminate\Contracts\Cache\Repository;

class AttributeCacheTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_should_store_given_attributes()
    {
        $repository = m::mock(Repository::class);
        $collection = m::mock(Collection::class);

        $repository->shouldReceive('forget')->once()->andReturn(true);
        $repository->shouldReceive('forever')->once();
        $collection->shouldReceive('groupBy')->with('code')->once();

        $cache = new AttributeCache($repository);

        $cache->set($collection);
    }

    /** @test */
    public function it_should_get_all_attributes()
    {
        $repository = m::mock(Repository::class);
        $repository->shouldReceive('get')->with('eav')->andReturn(collect());

        $cache = new AttributeCache($repository);

        $this->assertInstanceOf(Collection::class, $cache->all());
    }

    /** @test */
    public function it_should_get_an_attribute()
    {
        $collection = m::mock(Collection::class)->shouldDeferMissing();
        $collection->shouldReceive('get')->with('foo')->once()->andReturn('bar');

        $repository = m::mock(Repository::class);
        $repository->shouldReceive('get')->with('eav')->andReturn($collection);

        $cache = new AttributeCache($repository);

        $this->assertEquals('bar', $cache->get('foo'));
        $this->assertNull($cache->get('bar'));
    }
}