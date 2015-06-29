<?php namespace Devio\Propertier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyChoice extends Model {

    /**
     * Timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relationship the property the choice belongs to.
     *
     * @return BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}