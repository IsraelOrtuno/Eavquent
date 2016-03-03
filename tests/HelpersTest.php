<?php

use Devio\Eavquent\Agnostic\ConfigRepository;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        ConfigRepository::getInstance([
            'prefix' => [
                'package_tables' => 'eav_',
                'value_tables' => 'values_'
            ],
            'cache_key' => 'eav',
        ]);
    }

    /** @test */
    public function it_should_read_a_config_key()
    {
        $this->assertEquals('eav', eav_config('cache_key'));
        $this->assertEquals('eav_', eav_config('prefix.package_tables'));
    }

    /** @test */
    public function it_should_get_a_table_name()
    {
        $this->assertEquals('eav_foo', eav_table('foo'));
        $this->assertEquals('eav_bar', eav_table('bar'));
    }

    /** @test */
    public function it_should_get_a_value_table_name()
    {
        $this->assertEquals('eav_values_foo', eav_value_table('foo'));
        $this->assertEquals('eav_values_foo_bar', eav_value_table('fooBar'));
        $this->assertEquals('eav_values_foobar', eav_value_table('foobar'));
    }
}