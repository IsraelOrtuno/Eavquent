<?php namespace Devio\Propertier\Models;

use Devio\Propertier\Observers\PropertyValueObserver;
use Devio\Propertier\Properties\PropertyFactory;
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
     * Casting the value to a native PHP type. Will override the default
     * model casting function.
     *
     * @param string $key
     * @param mixed $value
     * @return bool|BaseCollection|mixed
     */
    protected function castAttribute($key, $value)
    {
        if ($key == 'value' && is_null($value))
        {
            return $value;
        }

        // Will cast the PropertyValue value to the type required int the
        // property definition class (if any). If no element is found,
        // this will act as normally and call the parent function.
        if ($property = $this->getPropertyRelation())
        {
            $value = PropertyFactory::make($property)
                                    ->value($this);

            return $value->getPlainValue();
        }

        return parent::castAttribute($key, $value);
    }

    /**
     * The property relation.
     *
     * @return Property
     */
    public function getPropertyRelation()
    {
        if ($this->propertyRelation)
        {
            return $this->propertyRelation;
        }

        return $this->property;
    }

    /**
     * @param Property $propertyRelation
     */
    public function setPropertyRelation($propertyRelation)
    {
        $this->propertyRelation = $propertyRelation;
    }
}