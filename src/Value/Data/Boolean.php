<?php

namespace Devio\Eavquent\Value\Data;

use Devio\Eavquent\Value\Value;

class Boolean extends Value
{
    /**
     * Atrribute casting.
     */
    protected $casts = [
        'content' => 'boolean',
    ];
}
