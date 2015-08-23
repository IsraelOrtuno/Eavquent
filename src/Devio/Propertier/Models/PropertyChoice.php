<?php
namespace Devio\Propertier\Models;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Observers\PropertyChoiceObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyChoice extends Model
{

    /**
     * Timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Booting the model.
     */
    protected static function boot()
    {
        static::observe(new PropertyChoiceObserver);
        parent::boot();
    }

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