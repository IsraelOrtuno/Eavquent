<?php

namespace Devio\Propertier;

use Illuminate\Support\ServiceProvider;
use Devio\Propertier\Listeners\SavingValues;

class PropertierServiceProvider extends ServiceProvider
{
    /**
     * Booting the service provider.
     */
    public function boot()
    {
        $basePath = __DIR__ . '/../..';

        // Publishing the package configuration file and migrations. This
        // will make them available from the main application folders.
        // They both are tagged in case they have to run separetely.
        $this->publishes(
            [$basePath . '/config/propertier.php' => config_path('propertier.php')], 'config'
        );
        $this->publishes(
            [$basePath . '/migrations/' => database_path('migrations')], 'migrations'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerProperties();
        $this->registerEventListeners();
    }

    /**
     * Will register the configured properties into the service container.
     */
    protected function registerProperties()
    {
        $properties = $this->app['config']->get('propertier.properties');

        Resolver::register($properties);
    }

    /**
     * Register the package configuration.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/propertier.php',
            'propertier'
        );
    }

    /**
     * Register model event listeners.
     */
    protected function registerEventListeners()
    {
        $events = $this->app['events'];

        // Listening for saving event on value models
        $events->listen('eloquent.saving: ' . Value::class, [new SavingValues, 'handle']);
    }
}
