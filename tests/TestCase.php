<?php

namespace Danielpk74\LaravelAuthStarter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAuthStarterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel_auth_starter_table.php.stub';
        $migration->up();
        */
    }
}
