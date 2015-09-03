<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    /**
     * List of attributes that open to mass assignment.
     *
     * @var array
     */
    public $fillable = [
        'type', 'name', 'multivalue', 'entity', 'default_value'
    ];

    /**
     * Relationship Property Values.
     *
     * @return HasMany
     */
    public function values()
    {
        return $this->hasMany(PropertyValue::class);
    }

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