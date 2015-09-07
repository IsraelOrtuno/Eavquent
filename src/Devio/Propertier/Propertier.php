<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Devio\Propertier\Services\PropertyReader;
use Devio\Propertier\Relations\HasManyProperties;
use Illuminate\Database\Eloquent\Relations\MorphMany;

abstract class Propertier extends Model
{
    /**
     * Minutes for caching table columns.
     *
     * @var mixed
     */
    protected $cachedColumns = 15;

    /**
     * The cache manager.
     *
     * @var \Illuminate\Cache\Repository|mixed
     */
    protected $cache;

    /**
     * Propertier constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->cache = $this->resolveCache();
    }

    /**
     * Relationship to the properties table.
     *
     * @return HasManyProperties
     */
    public function properties()
    {
        $instance = new Property;

        // We are using a self coded relation as there is no foreign key into
        // the properties table. The entity name will be used as a foreign
        // key to find the properties which belong to this entity item.
        return new HasManyProperties(
            $instance->newQuery(), $this, $this->getMorphClass()
        );
    }

    /**
     * Polimorphic relationship to the values table.
     *
     * @return MorphMany
     */
    public function values()
    {
        return $this->morphMany(PropertyValue::class, 'entity');
    }

    public function getPropertyRawValue($key)
    {
        return (new PropertyReader($this))->read($key);
    }

    /**
     * Will check if the key exists as registerd property.
     *
     * @param $key
     *
     * @return bool
     */
    public function isProperty($key)
    {
        // Checking if the key corresponds to any comlumn in the main entity
        // table. The table columns will be cached every 15 mins as it is
        // really unlikely to change. Caching will reduce the queries.
        if (in_array($key, $this->getTableColumns()))
        {
            return false;
        }

        // $key will be property when it does not belong to any relationship
        // name and it also exists into the entity properties collection.
        // This way it won't interfiere with the model base behaviour.
        return $this->getRelationValue($key)
            ? false
            : $this->getPropertiesKeyedBy()->has($key);
    }

    /**
     * Will return the properties collection keyed by name.
     * This way filtering will be much easier.
     *
     * @param string $keyBy
     *
     * @return Collection
     */
    public function getPropertiesKeyedBy($keyBy = 'name')
    {
        return $this->getRelationValue('properties')->keyBy($keyBy);
    }

    /**
     * Resolves the cache manager to use.
     *
     * @return \Illuminate\Cache\Repository|mixed
     */
    public function resolveCache()
    {
        return (new CacheResolver)->resolve();
    }

    /**
     * Will return the table columns.
     *
     * @return array
     */
    protected function getTableColumns()
    {
        $key = "propertier.{$this->getMorphClass()}";

        // Will add to a unique cache key the result of querying the model
        // schema columns. When trying to fetch the table columns, this
        // will check if there is cache before running any queries.
        return $this->cache->remember($key, $this->cachedColumns, function()
        {
            return $this->getConnection()
                        ->getSchemaBuilder()
                        ->getColumnListing($this->getTable());
        });
    }

    /**
     * Overriding magic method.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->isProperty($key))
        {
            return $this->getPropertyRawValue($key);
        }

        return parent::__get($key);
    }
}