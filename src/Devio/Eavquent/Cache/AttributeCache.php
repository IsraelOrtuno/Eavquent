<?php

namespace Devio\Eavquent\Cache;

use Illuminate\Cache\Repository;
use Devio\Eavquent\Contracts\AttributeCache as Cache;

class AttributeCache implements Cache
{
    /**
     * @var Repository
     */
    protected $cache;

    /**
     * AttributeCache constructor.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->cache->get(eav_config('cache_key'));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->all()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return $this->cache->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->cache->forget(eav_config('cache_key'));
    }
}