<?php

namespace Devio\Eavquent\Agnostic;


use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Devio\Eavquent\AttributeCache;
use Devio\Eavquent\Attribute\Cache;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

class BootEavquent
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * BootEavquent constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     *
     */
    public function boot()
    {
        if (! $this->container) {
            $this->container = new Container;
        }

        $this->registerBindings();

        Container::setInstance($this->container);
    }

    public function registerBindings()
    {
        $this->container->bind(AttributeCache::class, Cache::class);

        $this->container->singleton(\Illuminate\Contracts\Cache\Repository::class, function () {
            $store = new FileStore(new Filesystem, __DIR__ . '/../cache');

            return new Repository($store);
        });
    }
}