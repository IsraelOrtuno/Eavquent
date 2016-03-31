<?php

use Mockery as m;
use Devio\Eavquent\Eavquent;
use Devio\Eavquent\Interactor;
use Devio\Eavquent\Attribute\Manager;
use Illuminate\Contracts\Container\Container;

class EavquentTest extends PHPUnit_Framework_TestCase
{
    protected $entity;

    public function setUp()
    {
        $container = m::mock(Container::class);
        $manager = m::mock(Manager::class);
        $interactor = m::mock(Interactor::class);

        $this->entity = new EavquentStub;

        $container->shouldReceive('make')->with(Interactor::class, [$this->entity])->andReturn($interactor);
        $container->shouldReceive('make')->with(Manager::class)->andReturn($manager);

        $this->entity->setContainer($container);
    }

    /** @test */
    public function resolve_eavquent_dependences()
    {
        $this->assertInstanceOf(Container::class, $this->entity->getContainer());
        $this->assertInstanceOf(Interactor::class, $this->entity->getInteractor());
        $this->assertInstanceOf(Manager::class, $this->entity->getAttributeManager());
    }

    /** @test */
    public function get_content_of_eav_attribute()
    {
        $interactor = $this->entity->getInteractor();

        $interactor->shouldReceive('isAttribute')->with('foo')->andReturn(true);
        $interactor->shouldReceive('get')->with('foo')->andReturn('bar');

        $this->assertEquals('bar', $this->entity->getAttribute('foo'));
    }

    public function tearDown()
    {
        m::close();
    }
}

class EavquentStub
{
    use Eavquent;
}
