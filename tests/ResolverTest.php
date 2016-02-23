<?php

use Devio\Propertier\Field;
use Devio\Propertier\Resolver;
use Devio\Propertier\Values\StringValue;
use Devio\Propertier\Values\IntegerValue;

class ResolverTest extends PHPUnit_Framework_TestCase
{
    protected $resolver;

    public function setUp()
    {
        Resolver::register([
            'integer' => IntegerValue::class,
            'string' => StringValue::class
        ]);
        $this->resolver = new Resolver();
    }

    /** @test */
    public function it_resolves_a_field_by_model_instance()
    {
        $field = new Field(['type' => 'string']);
        $this->assertEquals(StringValue::class, $this->resolver->field($field));
    }

    /** @test */
    public function it_resolves_a_field_by_string()
    {
        $this->assertEquals(StringValue::class, $this->resolver->field('string'));
        $this->assertEquals(IntegerValue::class, $this->resolver->field('integer'));
    }

    /** @test */
    public function it_throws_exception_if_no_field_found()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->resolver->field('foo');
    }
}