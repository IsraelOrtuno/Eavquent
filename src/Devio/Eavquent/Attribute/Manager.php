<?php

namespace Devio\Eavquent\Attribute;

use Devio\Eavquent\Contracts\AttributeCache;

class Manager
{
    /**
     * @var AttributeCache
     */
    protected $cache;

    /**
     * @var AttributeRepository
     */
    protected $repository;

    /**
     * AttributeManager constructor.
     *
     * @param AttributeCache $cache
     * @param Repository $repository
     */
    public function __construct(AttributeCache $cache, Repository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    /**
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
     * @return $this
     */
    public function refresh()
    {
        $attributes = $this->repository->all();

        $this->cache->set($attributes->groupBy(Attribute::COLUMN_CODE));

        return $this;
    }

    /**
     * @return AttributeCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}