<?php
namespace Devio\Propertier;

use Devio\Propertier\Models\Value;
use Devio\Propertier\Models\Property;
use Devio\Propertier\Models\PropertyValue;
use Illuminate\Database\Eloquent\Collection;
use Devio\Propertier\Observers\PropertyObserver;
use Devio\Propertier\Relations\PropertierHasMany;

trait PropertierTrait
{

    /**
     * Properties should be at level 0 when converting to array.
     *
     * @var bool
     */
    protected $plainProperties = true;

    /**
     * Values to be deleted.
     *
     * @var array
     */
    protected $valueDeletionQueue;

    /**
     * Trait booter
     */
    public static function bootPropertierTrait()
    {
        static::observe(new PropertyObserver);
    }

    /**
     * Relationship between the entity and properties.
     * Will return the properties registered to the entity.
     *
     * @return Collection
     */
    public function properties()
    {
        $instance = new Property;

        return (new PropertierHasMany(
            $instance->newQuery(), $this, $this->getMorphClass()
        ));
    }

    /**
     * Relationship between the entity and property values.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->morphMany(PropertyValue::class, 'entity');
    }

    /**
     * Will check if a property exists in the current entity.
     *
     * @param $key
     *
     * @return bool
     */
    public function isProperty($key)
    {
        // Will check if the key requested belongs to a relationship in
        // the entity. If so, just return false as it would interfere
        // acting like a regular class instead of a relationship.
        if ($this->getRelationValue($key))
        {
            return false;
        }
        // Also we have to be sure that the relationship has been already
        // loaded into the entity before checking anything in it. This
        // also eager loads the properties relation into the entity.
        if ( ! $this->relationLoaded('properties'))
        {
            $this->load('properties');
        }

        return $this->properties->has($key);
    }

    /**
     * Find a property by key in the properties collection.
     *
     * @param $key
     * @return array
     */
    public function getProperty($key)
    {
        return $this->properties->get($key);
    }

    /**
     * The value deletion queue array.
     *
     * @return array
     */
    public function getValueDeletionQueue()
    {
        if (is_null($this->valueDeletionQueue))
        {
            $this->valueDeletionQueue = new Collection;
        }

        return $this->valueDeletionQueue;
    }

    /**
     * Add a new element to the deletion queue.
     *
     * @param $element
     */
    public function queueValueForDeletion($element)
    {
        $this->getValueDeletionQueue()->push($element);
    }

    /**
     * Dinamically retrive properties or regular attributes.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->isProperty($key))
        {
            return ValueGetter::make($this)->obtain($key);
        }

        return parent::__get($key);
    }
}