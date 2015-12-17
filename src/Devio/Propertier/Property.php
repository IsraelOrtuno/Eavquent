<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = [
        'type',
        'name',
        'multivalue',
        'entity',
        'default_value'
    ];

    /**
     * Check if the property accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return $this->multivalue;
    }
}
