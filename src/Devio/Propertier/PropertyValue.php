<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValue extends Model
{
    /**
     * Property Value fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'value', 'entity_type', 'entity_id', 'property_id'
    ];

    /**
     * The table every value will use.
     *
     * @var string
     */
    protected $table = 'property_values';

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
     * Casting to database string when setting.
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->setValue((string) $value);
    }

    /**
     * Casting from database string when getting.
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return $value;
    }

    /**
     * Easy setting the value property.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->attributes['value'] = $value;
    }

    /**
     * Will relate the value to the ID passed if it's not already set.
     *
     * @param $id
     */
    public function relatedOrRelateTo($id)
    {
        if (! $this->entity_id) {
            $this->entity_id = $id;
        }
    }

    public function transform()
    {
        $factory = new PropertyBuilder();

        return $factory->make(
            $this->property, $this->getAttributes()
        );
    }
}
