<?php

use Devio\Eavquent\Agnostic\ConfigRepository;

if (! function_exists('eav_table')) {
    /**
     * Get the base package table name.
     *
     * @param $name
     * @return string
     */
    function eav_table($name)
    {
        return eav_config('prefix.package_tables') . $name;
    }
}

if (! function_exists('eav_value_table')) {
    /**
     * Get the value table name.
     *
     * @param $name
     * @return string
     */
    function eav_value_table($name)
    {
        return eav_table(eav_config('prefix.value_tables')) . snake_case($name);
    }
}

if (! function_exists('eav_config')) {
    /**
     * Get a key from config.
     *
     * @param $key
     * @return mixed
     */
    function eav_config($key = null)
    {
        if (defined('LARAVEL_START')) {
            return config($key ? 'eavquent.' . $key : $key);
        }

        return ConfigRepository::getInstance()->get($key);
    }
}