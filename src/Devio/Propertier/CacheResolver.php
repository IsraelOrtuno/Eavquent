<?php namespace Devio\Propertier;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;

class CacheResolver
{
    public function resolve()
    {
        $app = $this->getApplication();

        if (is_a($app, 'Illuminate\Foundation\Application'))
        {
            return $app->make('cache');
        }

        return $this->createDefaultCacheManager();
    }

    protected function createDefaultCacheManager()
    {
        $cachePath = __DIR__ . '/../../storage/cache';

        $filesystem = new Filesystem;
        $storage = new FileStore($filesystem, $cachePath);

        return new Repository($storage);
    }

    protected function getApplication()
    {
        return function_exists('app') ? app() : null;
    }
}