<?php

use Mockery as m;
use Devio\Eavquent\Value\Value;
use Devio\Eavquent\Value\Builder;
use Devio\Eavquent\Value\Collection;
use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Model;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    protected $collection;

    public function setUp()
    {
        $items = [
            (new ExistingValueStub)->setRawAttributes(['content' => 'foo']),
            (new UnexistingValueStub)->setRawAttributes(['content' => 'bar'])
        ];

        $collection = new CollectionStub($items);

        $this->collection = $collection->link(new CollectionModelStub, m::mock(Attribute::class));
    }

    /** @test */
    public function add_new_value()
    {
        list($entity, $attribute, $builder) = [$this->collection->getEntity(), $this->collection->getAttribute(), $this->collection->getBuilder()];

        $builder->shouldReceive('build')->with($entity, $attribute, 'baz')
            ->andReturn((new UnexistingValueStub)->setRawAttributes(['content' => 'baz']));

        $this->collection->add('baz');

        $this->assertCount(3, $this->collection);
        $this->assertCount(1, $this->collection->where('content', 'baz'));
    }

    /** @test */
    public function replace_current_values()
    {
        list($entity, $attribute, $builder) = [$this->collection->getEntity(), $this->collection->getAttribute(), $this->collection->getBuilder()];

        $builder->shouldReceive('build')->with($entity, $attribute, 'baz')
            ->andReturn((new ExistingValueStub)->setRawAttributes(['content' => 'baz']));

        $this->collection->replace(['baz']);

        $this->assertCount(1, $this->collection);
        $this->assertCount(1, $this->collection->where('content', 'baz'));
        $this->assertCount(1, $this->collection->getReplaced());
        $this->assertCount(1, $this->collection->getReplaced()->where('content', 'foo'));

        $builder->shouldReceive('build')->with($entity, $attribute, 'qux')
            ->andReturn((new UnexistingValueStub)->setRawAttributes(['content' => 'qux']));

        $this->collection->replace(['qux']);

        $this->assertCount(1, $this->collection);
        $this->assertCount(1, $this->collection->where('content', 'qux'));
        $this->assertCount(2, $this->collection->getReplaced());
        $this->assertCount(1, $this->collection->getReplaced()->where('content', 'foo'));
        $this->assertCount(0, $this->collection->getReplaced()->where('content', 'bar'));
        $this->assertCount(1, $this->collection->getReplaced()->where('content', 'baz'));
    }

    public function tearDown()
    {
        m::close();
    }
}

class CollectionStub extends Collection
{
    protected $builder;

    public function getBuilder()
    {
        return $this->builder = $this->builder ?: m::mock(Builder::class);
    }
}

class CollectionModelStub extends Model
{
}

class ExistingValueStub extends Value
{
    public $exists = true;
}

class UnexistingValueStub extends Value
{
}
