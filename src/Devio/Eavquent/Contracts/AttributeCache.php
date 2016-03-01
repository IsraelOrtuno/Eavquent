<?php

namespace Devio\Eavquent\Contracts;

use Illuminate\Support\Collection;

interface AttributeCache
{
    public function exists();

    public function get();

    public function set(Collection $attributes);

    public function flush();
}