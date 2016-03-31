<?php

use Mockery as m;
use Devio\Eavquent\Eavquent;
use Devio\Eavquent\Interactor;
use Devio\Eavquent\Value\Value;
use Devio\Eavquent\Value\Builder;
use Illuminate\Database\Eloquent\Model;
use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Collection;

class InteractorTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function read_single_content()
    {
        $value = m::mock(Value::class);
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);

        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => new Attribute]));
        $value->shouldReceive('getContent')->andReturn('bar');
        $entity->shouldReceive('getRelationValue')->with('foo')->andReturn($value);

        $interactor = new Interactor($builder, $entity);

        $this->assertEquals('bar', $interactor->get('foo'));
    }

    /** @test */
    public function read_collection_content()
    {
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);
        $attribute = m::mock(Attribute::class);

        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => $attribute]));
        $attribute->shouldReceive('isCollection')->once()->andReturn(true);
        $entity->shouldReceive('getRelationValue')->with('foo')->andReturn(new Collection);

        $interactor = new Interactor($builder, $entity);

        $this->assertInstanceOf(Collection::class, $interactor->get('foo'));
    }

    /** @test */
    public function read_raw_object()
    {
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);

        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => new Attribute]));
        $entity->shouldReceive('getRelationValue')->with('foo')->andReturn('bar');

        $interactor = new Interactor($builder, $entity);

        $this->assertEquals('bar', $interactor->get('rawFooObject'));
        $this->assertEquals('bar', $interactor->get('rawfooobject'));
        $this->assertEquals('bar', $interactor->get('rawfooObject'));
        $this->assertEquals('bar', $interactor->get('rawFooobject'));
    }

    public function tearDown()
    {
        m::close();
    }
}

class InteractorStub extends Interactor
{
    public function getBuilder()
    {
        return $this->builder;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}

class InteractorModelStub extends Model
{
    use Eavquent;
}
