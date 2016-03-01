<?php

namespace Devio\Eavquent;

use Devio\Eavquent\Attribute\Attribute;
use Devio\Eavquent\Contracts\AttributeCache;

class AttributeManager
{
    /**
     * @var AttributeCache
     */
    protected $cache;

    /**
     * AttributeManager constructor.
     *
     * @param AttributeCache $cache
     */
    public function __construct(AttributeCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $entity
     * @param bool $fresh
     * @return mixed
     */
    public function get($entity = '*', $fresh = false)
    {
        if ($fresh) {
            $this->refreshAttributes();
        }

        return $entity == '*' ? $this->cache->all() : $this->cache->get($entity);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function refreshAttributes()
    {
        $this->cache->flush();

        $this->cache->set(Attribute::all());
    }
}