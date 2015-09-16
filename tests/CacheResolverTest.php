<?php

use Illuminate\Cache\Repository;
use Illuminate\Cache\CacheManager;
use Devio\Propertier\CacheResolver;

class CacheResolverTest extends TestCase
{
    
    public function test_it_will_resolve_the_laravel_cache_manager()
    {
        $resolver = new CacheResolver;
        $this->assertInstanceOf(CacheManager::class, $resolver->resolve());
    }

    public function test_it_will_resolve_the_default_cache_manager()
    {
        $resolver = new CacheResolver;
        $this->assertInstanceOf(Repository::class, $resolver->createDefaultCacheManager());
    }

}