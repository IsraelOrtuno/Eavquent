<?php

namespace Devio\Eavquent\Value\Data;

use Devio\Eavquent\Value\Value;

class Datetime extends Value
{
    /**
     * Casting content to date.
     *
     * @var array
     */
    protected $dates = ['content'];
}