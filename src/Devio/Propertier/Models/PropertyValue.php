<?php
namespace Devio\Propertier\Models;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Properties\PropertyFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Devio\Propertier\Observers\PropertyValueObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValue extends Model
{

    /**
     * Value field name.
     */
    const VALUE_FIELD = 'value';

    /**
     * Property Value fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'value', 'entity_type', 'entity_id', 'property_id'
    ];

    /**
     * The property relation model.
     *
     * @var Property
     */
    protected $propertyRelation;

    /**
     * Booting the model.
     */
    protected static function boot()
    {
        static::observe(new PropertyValueObserver);
        parent::boot();
    }

    /**
     * Relationship to the properties table.
     *
     * @return BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Polimorphic relation to the entity this value belongs to.
     *
     * @return MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Will relate the value to the ID passed if it's not already set.
     *
     * @param $id
     */
    public function relatedOrRelateTo($id)
    {
        if ( ! $this->entity_id)
        {
            $this->entity_id = $id;
        }
    }
}