<?php

namespace Danielpk74\LaravelAuthStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TestInstallCommand extends Command
{
    protected $signature = 'auth-starter:test-install';
    protected $description = 'Test the package installation in the current project';

    public function handle()
    {
        $this->info('🧪 Testing Laravel Auth Starter installation...');

        try {
            // Test config publishing
            $this->info('📝 Testing configuration publishing...');
            Artisan::call('vendor:publish', [
                '--provider' => 'Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider',
                '--tag' => 'auth-starter-config',
                '--force' => true
            ]);
            
            // Test migrations publishing  
            $this->info('📝 Testing migrations publishing...');
            Artisan::call('vendor:publish', [
                '--provider' => 'Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider', 
                '--tag' => 'auth-starter-migrations',
                '--force' => true
            ]);

            $this->info('✅ Package installation test completed successfully!');
            
            $this->info('');
            $this->info('🎯 Test the following endpoints:');
            $this->info('POST /api/auth/register - User registration');
            $this->info('POST /api/auth/login - User login');
            $this->info('GET /api/auth/user - Get authenticated user (requires token)');
            $this->info('PUT /api/auth/profile - Update profile (requires token)');
            $this->info('');
            $this->info('📁 Published files:');
            $this->info('- config/auth-starter.php');
            $this->info('- database/migrations/[migration files]');

        } catch (\Exception $e) {
            $this->error('❌ Installation test failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
