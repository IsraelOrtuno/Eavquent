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

        // Publishes the package configuration file when executing the artisan
        // command `vendor:publish`. This will create a propertier.php file
        // into the Laravel config path where can be updated if required.
        $this->publishes([
            __DIR__ . '/../../config/propertier.php' => config_path('propertier.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPropertier();
        $this->registerConfig();
        $this->registerProperties();
    }

    /**
     * Will register the propertier manager into the service container.
     */
    protected function registerPropertier()
    {
        $this->app->singleton('propertier', function ($app)
        {
            $eventDispatcher = $app->make('Illuminate\Contracts\Events\Dispatcher');

            return new Propertier($eventDispatcher);
        });
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