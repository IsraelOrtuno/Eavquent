<?php namespace Devio\Propertier;

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
        $this->publishes([
            __DIR__ . '/../../config/propertier.php' => config_path('propertier.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerProperties();
    }

    /**
     * Will register the configured properties into the service container.
     */
    protected function registerProperties()
    {
        $this->app->singleton('propertier.properties', function ($app)
        {
            return $app['config']->get('propertier.properties');
        });
    }

    /**
     * Register the package configuration.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/propertier.php', 'propertier'
        );
    }
}