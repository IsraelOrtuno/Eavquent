<?php

namespace Devio\Eavquent\Attribute;

use Devio\Eavquent\Contracts\AttributeCache;

class AttributeManager
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
     * @param AttributeRepository $repository
     */
    public function __construct(AttributeCache $cache, AttributeRepository $repository)
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
        return $entity == '*' ?
            $this->cache->all() : $this->cache->get($entity);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refresh()
    {
        $this->cache->flush();

        $this->cache->set($this->repository->all());

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
     * @return AttributeRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}