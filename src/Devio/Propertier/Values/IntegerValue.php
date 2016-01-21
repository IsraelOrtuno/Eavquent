<?php

namespace Devio\Propertier\Values;

use Devio\Propertier\Value as PropertyValue;

class IntegerValue extends PropertyValue
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
