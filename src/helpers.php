<?php

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
        return eav_table(eav_config('prefix.value_tables')) . $name;
    }
}

if (! function_exists('eav_config')) {
    /**
     * Get a key from config.
     *
     * @param $key
     * @return mixed
     */
    function eav_config($key)
    {
        $key = 'eavquent.' . $key;

        if (defined('LARAVEL_START')) {
            return config($key);
        }

        return array_get([
            'eavquent' => array_dot(require __DIR__ . '/config/eavquent.php')
        ], $key);
    }
}