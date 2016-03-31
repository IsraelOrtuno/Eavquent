<?php

use Mockery as m;
use Illuminate\Support\Collection;
use Devio\Eavquent\Attribute\Cache;
use Illuminate\Contracts\Cache\Repository;

class CacheTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function check_for_cache_existance()
    {
        $repository = m::mock(Repository::class);
        $repository->shouldReceive('has')->with('eav')->once()->andReturn(false);
        $cache = new Cache($repository);

        $this->assertFalse($cache->exists());

        $repository->shouldReceive('has')->with('eav')->once()->andReturn(true);
        $this->assertTrue($cache->exists());
    }

    /** @test */
    public function get_all_cached_attributes()
    {
        $collection = new Collection(['foo', 'bar']);
        $repository = m::mock(Repository::class);
        $repository->shouldReceive('get')->with('eav')->once()->andReturn($collection);
        $cache = new Cache($repository);

        $this->assertEquals($collection, $cache->get());
    }

    public function store_given_attributes()
    {
        $collection = new Collection(['foo', 'bar']);
        $repository = m::mock(Repository::class);
        $repository->shouldReceive('has')->with('eav')->once()->andReturn(false);
        $repository->shouldReceive('forget')->with('eav')->once();
        $repository->shouldReceive('forever')->with('eav', $collection)->once();

        $cache = new Cache($repository);

        $cache->set($collection);
    }

    public function tearDown()
    {
        m::close();
    }
}
