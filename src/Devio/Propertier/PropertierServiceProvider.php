<?php namespace Devio\Propertier;

use Illuminate\Support\ServiceProvider;
use Devio\Propertier\Services\ValueFormatter;
use Devio\Propertier\Validators\AbstractValidator;

class PropertierServiceProvider extends ServiceProvider
{
    /**
     * Botting the service provider.
     */
    public function boot()
    {
        // Will set the service container instance to any validator. This way
        // the full application is accessible from the validators in order
        // to provide the validation process with much more flexibility.
        $this->app->resolving(function (AbstractValidator $validator, $app)
        {
            $validator->setContainer($app);
        });

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
     *
     * @return void
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

    /**
     * Registering the value formatter.
     */
    protected function registerValueFormatter()
    {
        $this->app->singleton('propertier.formatter', function ($app)
        {
            return new ValueFormatter();
        });
    }
}