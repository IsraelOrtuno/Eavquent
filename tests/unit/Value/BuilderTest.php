<?php

use Mockery as m;
use Devio\Eavquent\Value\Builder;
use Devio\Eavquent\Value\Data\Varchar;
use Illuminate\Database\Eloquent\Model;
use Devio\Eavquent\Attribute\Attribute;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    protected $builder;

    public function setUp()
    {
        $this->builder = new Builder;
    }

    /** @test */
    public function build_class_based_on_attribute()
    {
        $entity = new BuilderEntityStub;
        $entity->setRawAttributes(['id' => 101]);
        $value = m::mock(Varchar::class);
        $attribute = m::mock(Attribute::class);

        $attribute->shouldReceive('getTypeInstance')->once()->andReturn($value);

        $attribute->shouldReceive('getForeignKey')->once()->andReturn('attribute_id');
        $attribute->shouldReceive('getKey')->once()->andReturn(202);

        $value->shouldReceive('setAttribute')->with('entity_id', 101)->once();
        $value->shouldReceive('setAttribute')->with('attribute_id', 202)->once();

        $value->shouldReceive('setContent')->with('foo')->once()->andReturn($value);

        $result = $this->builder->build($entity, $attribute, 'foo');

        $this->assertInstanceOf(Varchar::class, $result);
    }

    /** @test */
    public function ensure_entity_is_related()
    {
        $entity = new BuilderEntityStub;
        $entity->setRawAttributes(['id' => 101]);
        $attribute = m::mock(Attribute::class);
        $value = m::mock(Varchar::class);

        $attribute->shouldReceive('getForeignKey')->once()->andReturn('attribute_id');
        $attribute->shouldReceive('getKey')->once()->andReturn(202);

        $value->shouldReceive('setAttribute')->with('entity_id', 101)->once();
        $value->shouldReceive('setAttribute')->with('attribute_id', 202)->once();

        $this->builder->ensure($entity, $attribute, $value);
    }

    public function tearDown()
    {
        m::close();
    }
}

class BuilderEntityStub extends Model
{
}
