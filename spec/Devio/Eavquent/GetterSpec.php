<?php

namespace spec\Devio\Eavquent;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class GetterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Getter');
    }

    function it_should_identify_get_raw_attribute()
    {
        $this->isGetRawAttributeMutator('foo')->shouldBe(false);
        $this->isGetRawAttributeMutator('rawFoo')->shouldBe(false);
        $this->isGetRawAttributeMutator('fooObject')->shouldBe(false);

        $this->isGetRawAttributeMutator('rawfooobject')->shouldBe(true);
        $this->isGetRawAttributeMutator('rawfooObject')->shouldBe(true);
        $this->isGetRawAttributeMutator('rawFooObject')->shouldBe(true);
    }
}
