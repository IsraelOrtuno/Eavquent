<?php

namespace spec\Devio\Eavquent\Value;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Eavquent\Value\Data\Varchar;
use Devio\Eavquent\Attribute\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Value\Builder');
    }

    function it_should_build_a_value_model(Model $entity, Attribute $attribute, Varchar $value, BelongsTo $relation)
    {
        $attribute->getModelInstance()->shouldBeCalled()
            ->willReturn($value);

        $relation->associate($attribute)->shouldBeCalled();
        $relation->associate($entity)->shouldBeCalled();

        $value->entity()->shouldBeCalled()->willReturn($relation);
        $value->attribute()->shouldBeCalled()->willReturn($relation);
        $value->setContent('foo')->shouldBeCalled()->willReturn($value);

        $this->build($entity, $attribute, 'foo')->shouldBeAnInstanceOf(Varchar::class);
    }
}
