<?php

use Devio\Propertier\Property;
use Faker\Generator as FakerGenerator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as TestCaseBase;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class TestCase extends TestCaseBase
{
    use DatabaseMigrations;

    protected $company;
    protected $employee;

    /**
     * Setting up test
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('key:generate');
        $this->artisan('cache:clear');

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

    public function setUpProperties()
    {
        $this->company = factory(Company::class)->create();
        $this->employee = factory(Employee::class)->create();

        factory(Property::class)->create(['name' => 'foo']);
        factory(Property::class)->create(['name' => 'bar']);
        factory(Property::class)->create(['name' => 'baz']);
        factory(Property::class)->create([
                'name'   => 'qux',
                'entity' => 'Employee']
        );
        factory(Property::class)->create([
                'name'   => 'quux',
                'entity' => 'Employee']
        );
    }
}