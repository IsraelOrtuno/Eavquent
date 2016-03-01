<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value;

class StringValue extends Value
{
    /**
     * Table name.
     *
     * @var string
     */
    public $table = 'field_string_values';

    /**
     * Value casting.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string'
    ];
}
