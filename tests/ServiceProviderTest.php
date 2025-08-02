<?php

namespace Danielpk74\LaravelAuthStarter\Tests;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_can_load_the_service_provider()
    {
        $this->assertTrue(true);
        // Basic test to ensure the service provider can be loaded
        $this->assertInstanceOf(
            \Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider::class,
            $this->app->getProvider(\Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider::class)
        );
    }

    /** @test */
    public function it_registers_config()
    {
        $this->assertNotNull(config('auth-starter'));
    }

    /** @test */
    public function it_registers_commands()
    {
        $commands = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);
        
        $this->assertTrue($commands->all()['auth-starter:install'] instanceof \Danielpk74\LaravelAuthStarter\Commands\InstallCommand);
        $this->assertTrue($commands->all()['auth-starter:publish-assets'] instanceof \Danielpk74\LaravelAuthStarter\Commands\PublishAssetsCommand);
        $this->assertTrue($commands->all()['auth-starter:test-install'] instanceof \Danielpk74\LaravelAuthStarter\Commands\TestInstallCommand);
    }
}
