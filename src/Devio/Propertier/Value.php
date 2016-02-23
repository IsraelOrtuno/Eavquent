<?php

namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Listeners\ValueSaved;
use Devio\Propertier\Listeners\ValueSaving;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    /**
     * Property Value fillable attributes.
     *
     * @var array
     */
    protected $fillable = ['value', 'entity_type', 'partner_id', 'field_id'];

    /**
     * The table every value will use.
     *
     * @var string
     */
    protected $table = 'field_values';

    /**
     * The parent property.
     *
     * @var Property
     */
    protected $parentProperty = null;

    /**
     * The factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Booting the model.
     */
    public static function boot()
    {
        parent::boot();

        // Setting up the model event listeners. Much more elegant would be if
        // placed into the Service Provider. As this class is considered as
        // abstract, we have to set up the listeners at children classes.
//        static::saving(ValueSaving::class . '@handle');
    }

    /**
     * Relationship to the fields table.
     *
     * @return BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Polimorphic relation to the entity this value belongs to.
     *
     * @return MorphTo
     */
    public function partner()
    {
        return $this->morphTo();
    }

    /**
     * Check if the value is already casted.
     *
     * @return $this
     */
    public function isCasted()
    {
        return get_class($this) != Value::class;
    }

    /**
     * Remove null values from database.
     *
     * @return mixed
     */
    public static function flush()
    {
        return static::where('value', null)->delete();
    }

    /**
     * Set the model value.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
    }
}
