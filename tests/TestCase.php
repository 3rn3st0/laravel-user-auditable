<?php

namespace ErnestoCh\UserAuditable\Tests;

use ErnestoCh\UserAuditable\UserAuditableServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ErnestoCh\\UserAuditable\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            UserAuditableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Migrations para testing
        include_once __DIR__ . '/../database/migrations/create_test_tables.php.stub';
    }
}
