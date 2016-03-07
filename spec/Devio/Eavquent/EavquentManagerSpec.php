<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use Devio\Eavquent\Setter;
use Devio\Eavquent\Getter;
use PhpSpec\ObjectBehavior;

class EavquentManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\EavquentManager');
    }

    function let(Getter $getter, Setter $setter)
    {
        $this->beConstructedWith($getter, $setter);
    }
}
