<?php

namespace Devio\Eavquent\Contracts;

use Illuminate\Support\Collection;

interface AttributeCache
{
    public function all();

    public function get($attribute);

    public function set(Collection $attributes);

    public function flush();
}