<?php

use Mockery as m;
use Illuminate\Support\Collection;
use Devio\Eavquent\Attribute\Cache;
use Devio\Eavquent\Attribute\Manager;
use Devio\Eavquent\Attribute\Repository;

class ManagerTest extends PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager(m::mock(Cache::class), m::mock(Repository::class));
    }

    /** @test */
    public function get_all_attributes()
    {
        $this->manager->getCache()->shouldReceive('exists')->once()->andReturn(true);
        $this->manager->getCache()->shouldReceive('get');

        $this->manager->get();
    }

    /** @test */
    public function refresh_attributes_if_no_cache()
    {
        $collection = new Collection;
        $this->manager->getRepository()->shouldReceive('all')->once()->andReturn($collection);
        $this->manager->getCache()->shouldReceive('set')->with($collection)->once();
        $this->manager->getCache()->shouldReceive('exists')->once()->andReturn(false);
        $this->manager->getCache()->shouldReceive('get');

        $this->manager->get();
    }

    public function tearDown()
    {
        m::close();
    }
}
