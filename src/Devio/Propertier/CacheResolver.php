<?php namespace Devio\Propertier;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;

class CacheResolver
{
    /**
     * Will resolve the cache.
     *
     * @return Repository|mixed
     */
    public function resolve()
    {
        $app = function_exists('app') ? app() : null;

        // If the package is running under a full Laravel application, it will
        // just resolve it out of the Service Container. Otherwise, we will
        // provide a the Illuminate cache manager using the file driver.
        if (is_a($app, 'Illuminate\Foundation\Application'))
        {
            return $app->make('cache');
        }

        return $this->createDefaultCacheManager();
    }

    /**
     * Creates the default Illuminate cache manager instance.
     *
     * @return Repository
     */
    public function createDefaultCacheManager()
    {
        $cachePath = __DIR__ . '/../../storage/cache';

        return new Repository(
            new FileStore(new Filesystem, $cachePath)
        );
    }
}