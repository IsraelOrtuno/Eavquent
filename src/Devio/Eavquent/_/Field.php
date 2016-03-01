<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = ['type', 'name', 'multivalue', 'partner', 'default_value'];

    /*
     * Check if the field accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return (bool)$this->getAttribute('multivalue');
    }

    /**
     * Get the field name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->attributes['name'];
    }
}
