<?php

namespace Devio\Eavquent\Attribute;

use Devio\Eavquent\Contracts\AttributeCache as Cache;

class AttributeManager
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * The repository instance.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * AttributeManager constructor.
     *
     * @param Cache $cache
     * @param AttributeRepository $repository
     */
    public function __construct(Cache $cache, AttributeRepository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    /**
     * Get all the attributes registered or just for a single entity.
     *
     * @param string $entity
     * @return mixed
     */
    public function get($entity = '*')
    {
        if (! $this->cache->exists()) {
            $this->refresh();
        }

        $attributes = $this->cache->get();

        return $entity == '*' ?
            $attributes : $attributes->where(Attribute::COLUMN_ENTITY, $entity);
    }

    /**
     * Refresh the cache content.
     *
     * @return $this
     */
    public function refresh()
    {
        $this->cache->set($this->repository->all());

        return $this;
    }

    /**
     * Get the cache instance.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get the repository instance.
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}