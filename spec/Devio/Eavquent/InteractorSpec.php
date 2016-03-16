<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\Eavquent;
use Illuminate\Support\Collection;
use Devio\Eavquent\Value\VarcharValue;
use Illuminate\Database\Eloquent\Model;
use Devio\Eavquent\Attribute\Attribute;

class InteractorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Interactor');
    }

    function let(ReadModelStub $model)
    {
        $this->beConstructedWith($model);
    }

    function it_should_read_single_content(ReadModelStub $model, VarcharValue $value)
    {
        $model->getEntityAttributes()->willReturn(collect(['foo' => new Attribute()]));

        $value->getContent()->shouldBeCalled()->willReturn('bar');
        $model->getRelationValue('foo')->willReturn($value);

        $this->get('foo')->shouldBe('bar');
    }

    function it_should_read_collection_content(ReadModelStub $model, VarcharValue $value, Attribute $attribute, Collection $values)
    {
        $model->getEntityAttributes()->willReturn(collect(['foo' => $attribute]));

        $attribute->isCollection()->willReturn(true);

        $values->pluck('content', 'id')->shouldBeCalled()->willReturn(['foo' => 'bar']);
        $model->getRelationValue('foo')->willReturn($values);

        $this->get('foo')->shouldBe(['foo' => 'bar']);
    }

    function it_should_return_raw_object(ReadModelStub $model)
    {
        $model->getEntityAttributes()->willReturn(collect(['foo' => new Attribute()]));

        $model->getRelationValue('foo')->shouldBeCalledTimes(4)->willReturn('bar');

        $this->get('rawFooObject')->shouldBe('bar');
        $this->get('rawfooobject')->shouldBe('bar');
        $this->get('rawfooObject')->shouldBe('bar');
        $this->get('rawFooobject')->shouldBe('bar');
    }
}

class ReadModelStub extends Model
{
    use Eavquent;
}