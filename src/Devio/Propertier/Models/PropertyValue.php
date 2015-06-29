<?php namespace Devio\Propertier\Models;

use Devio\Propertier\Observers\PropertyValueObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValue extends Model {

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
     * Review: Is this needed?
     *
     * @return BelongsTo
     */
    public function choice()
    {
        return $this->belongsTo(PropertyChoice::class, 'value', 'id');
    }

    /**
     * @return Property
     */
    public function getPropertyRelation()
    {
        return $this->propertyRelation;
    }

    /**
     * @param Property $propertyRelation
     */
    public function setPropertyRelation($propertyRelation)
    {
        $this->propertyRelation = $propertyRelation;
    }
}