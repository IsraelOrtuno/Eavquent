<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value;

class IntegerValue extends Value
{
    /**
     * Table name.
     *
     * @var string
     */
    public $table = 'field_integer_values';

    /**
     * Value casting.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'integer'
    ];
}
