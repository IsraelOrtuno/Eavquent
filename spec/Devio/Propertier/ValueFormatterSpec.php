<?php

namespace spec\Devio\Propertier;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Illuminate\Support\Collection;
use Devio\Propertier\Models\Property;
use Devio\Propertier\Properties\Factory;
use Devio\Propertier\Properties\Integer;
use Devio\Propertier\Models\PropertyValue;

class ValueFormatterSpec extends ObjectBehavior
{

    function let(Factory $factory)
    {
        $this->beconstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\ValueFormatter');
    }

    function it_will_format_a_single_value_property(Factory $factory)
    {
        $model = new TestPropertyModel(1, 101);

        $factory->make($model->property)
                ->shouldBeCalled()
                ->willReturn(new Integer());

        $this->format($model)->shouldBeEqualTo(101);
    }

    function it_will_format_an_array_or_collection_of_value_properties(Factory $factory)
    {
        $modelA = new TestPropertyModel(1, 101);
        $modelB = new TestPropertyModel(2, 202);
        $modelArray = [$modelA, $modelB];
        $modelCollection = new Collection($modelArray);
        $factory->make($modelA->property)->shouldBeCalled()->willReturn(new Integer());
        $factory->make($modelB->property)->shouldBeCalled()->willReturn(new Integer());

        $this->format($modelArray)->toArray()->shouldBeEqualTo([
            '1' => 101,
            '2' => 202
        ]);

        $this->format($modelCollection)->toArray()->shouldBeEqualTo([
            '1' => 101,
            '2' => 202
        ]);
    }
}

class TestPropertyModel extends PropertyValue
{
    public $id;

    public $value;

    public $property;

    public function __construct($id, $value)
    {
        $this->property = new Property(['type' => 'integer']);
        $this->id = $id;
        $this->value = $value;
    }

}
