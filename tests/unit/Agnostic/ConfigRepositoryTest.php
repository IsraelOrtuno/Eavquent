<?php

use Devio\Eavquent\Agnostic\ConfigRepository;

class ConfigRepositoryTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function merge_default_config_values()
    {
        $config = ConfigRepository::getInstance(['foo' => 'foo']);

        $this->assertTrue($config->has('foo'));
        $this->assertFalse($config->has('bar'));

        ConfigRepository::getInstance(['bar' => 'bar']);
        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('bar'));
    }
    
    /** @test */
    public function create_single_instance()
    {
        $instance = ConfigRepository::getInstance(['foo' => 'bar']);

        $this->assertEquals($instance, ConfigRepository::getInstance());
    }
}
