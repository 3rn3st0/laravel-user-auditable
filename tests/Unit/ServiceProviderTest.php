<?php

namespace ErnestoCh\UserAuditable\Tests\Unit;

use ErnestoCh\UserAuditable\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_the_service_provider()
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(
            'ErnestoCh\\UserAuditable\\Providers\\UserAuditableServiceProvider',
            $providers
        );
    }

    /** @test */
    public function it_registers_schema_macros()
    {
        $this->assertTrue(
            \Illuminate\Database\Schema\Blueprint::hasMacro('userAuditable')
        );

        $this->assertTrue(
            \Illuminate\Database\Schema\Blueprint::hasMacro('fullAuditable')
        );
    }

    /** @test */
    public function it_provides_config_file()
    {
        $config = config('user-auditable');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('enabled_macros', $config);
    }
}
