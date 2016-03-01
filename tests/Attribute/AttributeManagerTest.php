<?php

use Mockery as m;
use Illuminate\Support\Collection;
use Devio\Eavquent\Cache\AttributeCache;
use Devio\Eavquent\Attribute\AttributeManager;
use Devio\Eavquent\Attribute\AttributeRepository;

class AttributeManagerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_should_register_an_attribute()
    {
        $type = 'varchar';



    }

    /** @test */
    public function it_should_get_all_attributes()
    {
        $manager = $this->getAttributeManager();

        $manager->getCache()->shouldReceive('all')->once();
        $manager->get();

        $manager->getCache()->shouldReceive('all')->once();
        $manager->get('*');
    }

    /** @test */
    public function it_should_get_a_single_attribute()
    {
        $manager = $this->getAttributeManager();

        $manager->getCache()->shouldReceive('get')->with('foo')->once();

        $manager->get('foo');
    }

    /** @test */
    public function it_should_refresh_attributes_cache()
    {
        $manager = $this->getAttributeManager();

        $manager->getCache()->shouldIgnoreMissing();
        $manager->getRepository()->shouldReceive('all')->once()->andReturn(new Collection);

        $manager->get(null, true);
    }

    protected function getAttributeManager()
    {
        return new AttributeManager(
            m::mock(AttributeCache::class),
            m::mock(AttributeRepository::class)
        );
    }
}