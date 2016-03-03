<?php

namespace spec\Devio\Eavquent\Agnostic;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigRepositorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devio\Eavquent\Agnostic\ConfigRepository');
    }

    function it_should_merge_with_default_values()
    {
        self::getInstance()->has('foo')->shouldBe(false);
        self::getInstance()->has('cache_key')->shouldBe(true);

        self::getInstance(['foo' => 'bar']);
        self::getInstance()->has('foo')->shouldBe(true);
    }

    function it_should_create_a_single_instance()
    {
        $instance = self::getInstance(['foo' => 'bar']);

        self::getInstance()->shouldBe($instance);
    }
}
