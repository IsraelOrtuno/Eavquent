<?php

namespace Devio\Propertier;

use Illuminate\Support\ServiceProvider;

class PropertierServiceProvider extends ServiceProvider
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
            [$this->base('config/propertier.php') => config_path('propertier.php')], 'config'
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
        $this->registerValueTypes();
    }

    /**
     * Will register the configured properties into the service container.
     */
    protected function registerValueTypes()
    {
        $properties = $this->app['config']->get('eavquent.fields');

        Resolver::register($properties);
    }

    /**
     * Register the package configuration.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom($this->base('config/propertier.php'), 'propertier');
    }

    /**
     * Get the base path.
     *
     * @param $path
     * @return string
     */
    protected function base($path)
    {
        return __DIR__ . "/../../{$path}";
    }
}
