<?php

namespace Devio\Eavquent\Attribute;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    // Attribute type column
    const COLUMN_TYPE = 'type';
    // Entity the attribute belongs to.
    const COLUMN_ENTITY = 'entity';
    // Attribute default value column.
    const COLUMN_DEFAULT_VALUE = 'default_value';

    /**
     * Model timestamps.
     *
     * @var bool
     */
    public $timestamps = false;
}