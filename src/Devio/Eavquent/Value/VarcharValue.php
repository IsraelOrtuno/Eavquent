<?php

namespace Devio\Eavquent\Value;

class VarcharValue extends Value
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
