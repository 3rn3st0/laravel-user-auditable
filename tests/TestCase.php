<?php

namespace ErnestoCh\UserAuditable\Tests;

use ErnestoCh\UserAuditable\Providers\UserAuditableServiceProvider;
use ErnestoCh\UserAuditable\Tests\TestModels\TestUser;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Configure authentication guard
        config(['auth.providers.users.model' => TestUser::class]);
    }

    protected function getPackageProviders($app)
    {
        return [
            UserAuditableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // MySQL configuration for local development
        if (env('TEST_DB_CONNECTION', 'sqlite') === 'mysql') {
            config()->set('database.default', 'mysql');
            config()->set('database.connections.mysql', [
                'driver' => 'mysql',
                'host' => env('TEST_DB_HOST', '127.0.0.1'),
                'port' => env('TEST_DB_PORT', '3306'),
                'database' => env('TEST_DB_DATABASE', 'test_database'),
                'username' => env('TEST_DB_USERNAME', 'root'),
                'password' => env('TEST_DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);
        } else {
            // SQLite configuration for CI
            config()->set('database.default', 'sqlite');
            config()->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        }

        // Authentication configuration
        config()->set('auth.defaults.guard', 'web');
        config()->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        config()->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => TestUser::class,
        ]);
    }
}
