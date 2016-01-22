<?php

namespace spec\Devio\Propertier;

use RuntimeException;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Devio\Propertier\Factory;
use Devio\Propertier\Property;

class FactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Propertier\Factory');
    }

    public function let()
    {
        Factory::register([
            'integer' => 'IntegerClass',
            'string' => 'StringClass'
        ]);
    }

    public function it_resolves_by_model_instance()
    {
        $property = new Property(['type' => 'string']);

        $this->property($property)->shouldReturn('StringClass');
    }

    public function it_resolves_by_string()
    {
        $this->property('string')->shouldReturn('StringClass');
        $this->property('integer')->shouldReturn('IntegerClass');
    }

    public function it_throws_exception_if_no_binding_is_found()
    {
        $this->shouldThrow(RuntimeException::class)->during('property', ['foo']);
    }
}
