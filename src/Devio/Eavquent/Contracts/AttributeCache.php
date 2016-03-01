<?php

namespace Devio\Eavquent\Contracts;

interface AttributeCache
{
    public function all();

    public function get($key, $default = null);

    public function set($key, $value);

    public function flush();
}