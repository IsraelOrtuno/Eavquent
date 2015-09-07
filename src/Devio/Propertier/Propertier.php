<?php
namespace Devio\Propertier;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Services\ValueGetter;
use Devio\Propertier\Services\ValueSetter;
use Illuminate\Database\Eloquent\Collection;
use Devio\Propertier\Relations\HasManyProperties;
use Devio\Propertier\Observers\PropertierObserver;

abstract class Propertier extends Model
{
    /**
     * Set if eager loading the properties and values automatically.
     *
     * @var bool
     */
    protected $propertierEagerLoad = true;

    /**
     * Array of property names to be merged with fillable attributes.
     *
     * @var array
     */
    protected $propertyNames = [];

    /**
     * Values to be deleted.
     *
     * @var array
     */
    protected $valueDeletionQueue;

    /**
     * Propertier constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->bootPropertier();

        parent::__construct($attributes);
    }

    /**
     * Custom trait booter. Will register the observers.
     */
    protected static function boot()
    {
        static::observe(new PropertierObserver);

        parent::boot();
    }

    protected function bootPropertier()
    {
        $this->registerWiths();

        $this->registerPropertyNames();
    }

    /**
     * Booting Propertier.
     */
    protected function bootPropertierIfNotBooted()
    {
    }

    /**
     * Will include the propertier relations into the $with array.
     */
    protected function registerWiths()
    {
        if ($this->propertierEagerLoad)
        {
            $this->with = array_merge(
                $this->with, ['properties', 'values']
            );
        }
    }

    protected function registerPropertyNames()
    {
        $type = $this->getMorphClass();

        $this->propertyNames = Cache::remember("propertier.$type.names", 7200, function () use ($type)
        {
            $properties = Property::where('entity', $type)
                                  ->get()
                                  ->pluck('name');

            return $properties->toArray();
        });
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

        return new HasManyProperties(
            $instance->newQuery(), $this, $this->getMorphClass()
        );
    }

    /**
     * Relationship between the entity and property values.
     *
     * @return mixed
     */
    public function values()
    {
        return $this->morphMany(PropertyValue::class, 'entity');

        //        $instance = new PropertyValue();
        //
        //        // We are manually creating the relationship object instead of using
        //        // the Eloquent methods for this purpose. All these information is
        //        // needed for creating our extended MorphMany relation object.
        //        list($type, $id) = $this->getMorphs('entity', null, null);
        //
        //        $table = $instance->getTable();
        //
        //        return new MorphManyValues(
        //            $instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $this->getKeyName()
        //        );
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
        // Will check if the key requested belongs either to a relationship in
        // the entity or a column name. If so, just return false as we don't
        // want to interfere with the main entity attributes or relations.
        if ($this->getRelationValue($key) || in_array($key, $this->getTableColumns()))
        {
            return false;
        }

        // As every trait operation will have to cross this function to check
        // if it really needs to be performed, this looks a good place for
        // bootstrapping the trait and simulate a regular constructor.
        //        $this->bootPropertierIfNotBooted();

        return $this->getPropertiesKeyed()->has($key);
    }

    /**
     * Find a property model by type in the properties collection.
     *
     * @param $type
     *
     * @return array
     */
    public function getPropertyObject($type)
    {
        return $this->getPropertiesKeyed()->get($type);
    }

    /**
     * If it is a valid property attribute, will provide it. If not found
     * will call the regular model get function.
     *
     * @param $key
     *
     * @return Collection
     */
    public function getProperty($key)
    {
        if ($this->isProperty($key))
        {
            return (new ValueGetter)->get($this, $key);
        }

        return parent::__get($key);
    }

    /**
     * Setting a property or a regular eloquent attribute.
     *
     * @param $key
     * @param $value
     *
     * @return Services\PropertyValue|mixed|void
     */
    public function setProperty($key, $value)
    {
        if ($this->isProperty($key))
        {
            return (new ValueSetter)->entity($this)
                                    ->set($key, $value);
        }

        return parent::__set($key, $value);
    }

    /**
     * Will return the properties collection keyed by name.
     * This way filtering will be much easier.
     *
     * @return mixed
     */
    protected function getPropertiesKeyed()
    {
        return $this->getRelationValue('properties')
                    ->keyBy('name');
    }

    /**
     * Will return an array with containing the registered property names.
     *
     * @param bool $array
     *
     * @return mixed
     */
    //    public function getPropertyNames($array = false)
    //    {
    //        $properties = $this->getRelationValue('properties')
    //                           ->pluck('name');
    //
    //        return $array ? $properties->toArray() : $properties;
    //    }

    /**
     * Overriding Eloquents isFillable. Will return true if the key is a
     * property, otherwise will use the parents method.
     *
     * @param $key
     *
     * @return mixed
     */
    public function isFillable($key)
    {
        return $this->isProperty($key) ?: parent::isFillable($key);
    }

    /**
     * For fetching the items that are fillable. Merging properties into the
     * fillable attributes and calling the parent.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    protected function fillableFromArray(array $attributes)
    {
        $this->fillable = array_merge(
            $this->fillable, $this->propertyNames
        );

        return parent::fillableFromArray($attributes);
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
     * Deletes any element in the deletion queue.
     */
    public function executeDeletionQueue()
    {
        $queue = $this->getValueDeletionQueue();

        // Will delete all the rows that matches any of the ids stored in the
        // deletion queue variable. Will check for elements in this queue
        // to avoid performing a query if no element has to be deleted.
        if ($queue->count())
        {
            $deletionKeys = $queue->pluck('id')->toArray();

            PropertyValue::whereIn('id', $deletionKeys)
                         ->delete();
        }
    }

    /**
     * Will return the table columns.
     *
     * NOTE: IMPORTANTE. Esto actualmente está generando una consulta
     * NOTE: a la base de datos cada vez que isProperty se ejecuta
     * NOTE: por lo que el rendimiento es pésimo. Considerar cachear
     * NOTE: o que hacer con esto.
     *
     * @return mixed
     */
    protected function getTableColumns()
    {
        return $this->getConnection()
                    ->getSchemaBuilder()
                    ->getColumnListing($this->getTable());
    }

    /**
     * Dinamically setting a property or eloquent attribute.
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->setProperty($key, $value);
    }

    /**
     * Dinamically retrive a property or eloquent attribute.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getProperty($key);
    }
}