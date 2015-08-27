<?php

namespace spec\Devio\Propertier\Services;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Illuminate\Events\Dispatcher;
use Devio\Propertier\Validators\CreateProperty;

class PropertyCreatorSpec extends ObjectBehavior
{
    function let(Dispatcher $event, CreateProperty $validator)
    {
        $this->beConstructedWith($event, $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\Services\PropertyCreator');
    }

    function it_registers_a_new_property(CreateProperty $validator)
    {
        $attributes = ['name' => 'website', 'type' => 'integer', 'entity' => 'Company'];

        $validator->validate($attributes)->shouldBeCalled()->willReturn(false);

        $this->create($attributes);
    }
}
