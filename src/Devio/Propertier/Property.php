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
    public $fillable = ['type', 'name', 'multivalue', 'entity', 'default_value'];

    /**
     * The property values relationship.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->hasMany(Value::class);
    }

    /**
     * Replicates a model and set it as existing.
     *
     * @return mixed
     */
    public function replicateExisting()
    {
        $instance = parent::replicate(['*']);
        $instance->exists = $this->exists;

        return $instance;
    }

    /**
     * Check if the property accepts multiple values.
     *
     * @return mixed
     */
    public function isMultivalue()
    {
        return $this->getAttribute('multivalue');
    }
}
