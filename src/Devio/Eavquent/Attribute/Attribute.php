<?php

namespace Devio\Eavquent\Attribute;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * Column relating attribute to entity.
     */
    const COLUMN_ENTITY = 'entity';

    /**
     * Model timestamps.
     *
     * @var bool
     */
    public $timestamps = false;
}