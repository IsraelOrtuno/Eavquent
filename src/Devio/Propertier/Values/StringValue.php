<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value;

class StringValue extends Value
{
    /**
     * Value casting.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string'
    ];
}
