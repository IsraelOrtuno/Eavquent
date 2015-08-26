<?php

use Faker\Generator as FakerGenerator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as TestCaseBase;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class TestCase extends TestCaseBase
{

    use DatabaseMigrations;

    /**
     * Setting up test
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('key:generate');

        $this->setUpDatabase();
        $this->setUpFactories();
        $this->setUpServiceprovider();
    }

    /**
     * Setting up database
     */
    protected function setUpDatabase()
    {
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate', [
            '--path' => '../../../src/migrations'
        ]);

        $this->artisan('migrate', [
            '--path' => '../../../tests/support/migrations'
        ]);
    }

    /**
     * Register the service provider and publishes the config.
     */
    protected function setUpServiceprovider()
    {
        $this->app->register('Devio\Propertier\PropertierServiceProvider');

        $this->artisan('vendor:publish');
    }

    /**
     * Will load the model factories.
     */
    protected function setUpFactories()
    {
        $this->app->singleton(EloquentFactory::class, function ($app)
        {
            $faker = $app->make(FakerGenerator::class);

            return EloquentFactory::construct($faker, __DIR__ . '/factories');
        });
    }

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}