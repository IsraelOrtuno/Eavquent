<?php namespace Devio\Propertier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model {

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
     * Relationship Property Choices.
     *
     * @return HasMany
     */
    public function choices()
    {
        return $this->hasMany(PropertyChoice::class);
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

    /**
     * Determinates if the property might handle a limited set of values.
     *
     * @return mixed
     */
    public function isChoices()
    {
        return $this->choices;
    }
}