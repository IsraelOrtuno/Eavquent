<?php

namespace Devio\Eavquent\Value;

class VarcharValue extends AbstractValue
{
    /**
     * Table name.
     *
     * @var string
     */
    public $table = 'eav_values_varchar';

    /**
     * Value casting.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string'
    ];
}
