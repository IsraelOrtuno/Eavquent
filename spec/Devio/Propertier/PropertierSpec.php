<?php

namespace spec\Devio\Propertier;

use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Illuminate\Contracts\Events\Dispatcher;
use Devio\Propertier\Validators\RegisterProperty;

class PropertierSpec extends ObjectBehavior
{
    function let(Dispatcher $event)
    {
        $this->beConstructedWith($event);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\Propertier');
    }
    
    function it_registers_a_new_property(RegisterProperty $validator)
    {
        $attributes = ['name' => 'website', 'type' => 'integer', 'entity' => 'Company'];

        $validator->validate()->willReturn(false);

        $this->register($attributes);
    }
}