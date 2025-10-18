<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Twirelab\LaravelRouter\LaravelRouterServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear routes before each test
        $this->app['router']->getRoutes()->refreshNameLookups();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelRouterServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Loader' => \Twirelab\LaravelRouter\Facades\Loader::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup laravel-router config
        $app['config']->set('laravel-router', [
            'active_versions' => [1, 2],
            'version_in_url' => true,
            'version_prefix' => 'v',
        ]);
    }
}
