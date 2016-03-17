<?php

namespace Devio\Eavquent;

use Devio\Eavquent\Attribute\Cache;
use Illuminate\Support\ServiceProvider;
use Devio\Eavquent\Value\Builder as ValueBuilder;

class EavquentServiceProvider extends ServiceProvider
{
    /**
     * Booting the service provider.
     */
    public function boot()
    {
        // Publishing the package configuration file and migrations. This
        // will make them available from the main application folders.
        // They both are tagged in case they have to run separetely.
        $this->publishes(
            [$this->base('config.php') => config_path('eavquent.php')], 'config'
        );
        $this->publishes(
            [$this->base('migrations/') => database_path('migrations')], 'migrations'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerBindings();
    }

    /**
     * Register the package configuration.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom($this->base('config.php'), 'eavquent');
    }

    /**
     * Register container bindings.
     */
    protected function registerBindings()
    {
        $this->app->bind(AttributeCache::class, Cache::class);

        $this->app->bind(Interactor::class, function ($app, $params) {
            $builder = $this->app->make(ValueBuilder::class);

            return new Interactor($builder, $params[0]);
        });
    }

    /**
     * Get the base path.
     *
     * @param $path
     * @return string
     */
    protected function base($path)
    {
        return __DIR__ . "/../{$path}";
    }
}