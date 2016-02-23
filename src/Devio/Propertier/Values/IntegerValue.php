<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value;

class IntegerValue extends Value
{
    /**
     * Value casting.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'integer'
    ];
}
