<?php

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as TestCaseBase;

class TestCase extends TestCaseBase {

    /**
     * Setting up test
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * Setting up database
     */
    protected function setUpDatabase()
    {
        $db = new DB();

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => ''
        ], 'sqlite-testing');

        $db->bootEloquent();

        $db->setAsGlobal();

        $this->runDatabaseMigrations();
    }

    public function runDatabaseMigrations()
    {
        $this->artisan('migrate', ['--path', __DIR__ . '/../src/migrations']);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}