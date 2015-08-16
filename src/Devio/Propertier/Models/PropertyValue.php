<?php namespace Devio\Propertier\Models;

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
//    protected function castAttribute($key, $value)
//    {
//        if ($this->isValueField($key) && is_null($value))
//        {
//            return $value;
//        }
//
//        $propertyRelation = $this->getPropertyRelation();
//
//        // Will cast the PropertyValue value to the type required int the
//        // property definition class (if any). If no element is found,
//        // this will act as normally and call the parent function.
//        if ($property = PropertyFactory::make($propertyRelation))
//        {
//            return $property->value($this)
//                            ->getPlainValue();
//        }
//
//        return parent::castAttribute($key, $value);
//    }

    /**
     * Forcing false for casting.
     *
     * @param string $key
     * @return bool
     */
    protected function hasCast($key)
    {
        return false;
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
     * Sets the property relation.
     *
     * @param Property $propertyRelation
     */
    public function setPropertyRelation($propertyRelation)
    {
        $this->propertyRelation = $propertyRelation;
    }
}