<?php

use Mockery as m;
use Devio\Eavquent\Value\Builder;
use Devio\Eavquent\Value\Data\Varchar;
use Illuminate\Database\Eloquent\Model;
use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $value = m::mock(Varchar::class);
        $attribute = m::mock(Attribute::class);
        $relation = m::mock(BelongsTo::class)->shouldIgnoreMissing();

        $attribute->shouldReceive('getModelInstance')->once()->andReturn($value);
        $value->shouldReceive('entity')->andReturn($relation);
        $value->shouldReceive('attribute')->andReturn($relation);

        $value->shouldReceive('setContent')->once()->andReturn($value);

        $result = $this->builder->build($entity, $attribute, 'foo');

        $this->assertInstanceOf(Varchar::class, $result);
    }

    /** @test */
    public function ensure_entity_is_related()
    {
        $entity = new BuilderEntityStub;
        $attribute = m::mock(Attribute::class);
        $relation = m::mock(BelongsTo::class);
        $value = m::mock(Varchar::class);

        $value->shouldReceive('entity')->andReturn($relation);
        $value->shouldReceive('attribute')->andReturn($relation);

        $relation->shouldReceive('associate')->with($attribute)->once();
        $relation->shouldReceive('associate')->with($entity)->once();

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
