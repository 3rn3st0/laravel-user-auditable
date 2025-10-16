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
        // Use SQLite consistently for all environments
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

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
