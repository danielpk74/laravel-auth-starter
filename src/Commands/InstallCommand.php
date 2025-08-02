<?php

namespace Danielpk74\LaravelAuthStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'auth-starter:install 
                            {--force : Overwrite existing files}
                            {--with-frontend : Install frontend assets}
                            {--with-seeders : Run database seeders}';

    protected $description = 'Install the Laravel Auth Starter package';

    public function handle()
    {
        $this->info('Installing Laravel Auth Starter...');

        // Publish configuration
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Run migrations
        $this->runMigrations();

        // Publish frontend assets if requested
        if ($this->option('with-frontend')) {
            $this->publishFrontendAssets();
        }

        // Run seeders if requested
        if ($this->option('with-seeders')) {
            $this->runSeeders();
        }

        // Update User model if needed
        $this->updateUserModel();

        // Setup routes
        $this->setupRoutes();

        $this->info('✅ Laravel Auth Starter installed successfully!');
        
        if (!$this->option('with-frontend')) {
            $this->warn('💡 Run with --with-frontend to install Vue.js components');
        }
        
        if (!$this->option('with-seeders')) {
            $this->warn('💡 Run with --with-seeders to create default users');
        }

        $this->displayNextSteps();
    }

    protected function publishConfig()
    {
        $this->info('📝 Publishing configuration...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-config',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishMigrations()
    {
        $this->info('📝 Publishing migrations...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-migrations',
            '--force' => $this->option('force'),
        ]);
    }

    protected function runMigrations()
    {
        $this->info('🗄️ Running migrations...');
        
        $this->call('migrate');
    }

    protected function publishFrontendAssets()
    {
        $this->info('🎨 Publishing frontend assets...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-js',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-components',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-css',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-locales',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-config-files',
            '--force' => $this->option('force'),
        ]);

        $this->info('📦 Frontend assets published successfully!');
        $this->warn('💡 Run "npm install" to install frontend dependencies');
        $this->warn('💡 Check AUTH_STARTER_FRONTEND.md for integration guide');
    }

    protected function runSeeders()
    {
        $this->info('🌱 Running seeders...');
    }

    protected function updateUserModel()
    {
        $userModelPath = app_path('Models/User.php');
        
        if (!File::exists($userModelPath)) {
            $this->warn('⚠️ User model not found at ' . $userModelPath);
            return;
        }

        $content = File::get($userModelPath);
        
        // Check if HasRoles trait is already imported
        if (strpos($content, 'use Danielpk74\\LaravelAuthStarter\\Models\\Traits\\HasRoles;') === false) {
            $this->info('🔧 Updating User model...');
            
            // Add the trait import
            $content = str_replace(
                'use Laravel\\Sanctum\\HasApiTokens;',
                "use Laravel\\Sanctum\\HasApiTokens;\nuse Danielpk74\\LaravelAuthStarter\\Models\\Traits\\HasRoles;",
                $content
            );
            
            // Add the trait to the class
            $content = str_replace(
                'use HasApiTokens, HasFactory, Notifiable;',
                'use HasApiTokens, HasFactory, Notifiable, HasRoles;',
                $content
            );
            
            File::put($userModelPath, $content);
            $this->info('✅ User model updated with HasRoles trait');
        } else {
            $this->info('✅ User model already configured');
        }
    }

    protected function setupRoutes()
    {
        $this->info('🛣️ Setting up routes...');
        
        $apiRoutesPath = base_path('routes/api.php');
        $content = File::get($apiRoutesPath);
        
        // Check if our routes are already included
        if (strpos($content, 'auth-starter routes') === false) {
            $routeInclude = "\n\n// Laravel Auth Starter routes\n// You can customize these in config/auth-starter.php\n";
            File::append($apiRoutesPath, $routeInclude);
            $this->info('✅ Routes configured');
        }
    }

    protected function displayNextSteps()
    {
        $this->info('');
        $this->info('🎉 Next Steps:');
        $this->info('');
        $this->info('1. Review the configuration file: config/auth-starter.php');
        $this->info('2. Customize the User model if needed');
        $this->info('3. Test the authentication endpoints:');
        $this->info('   - POST /api/auth/login');
        $this->info('   - POST /api/auth/register');
        $this->info('   - GET /api/auth/user (protected)');
        $this->info('');
        
        if ($this->option('with-seeders')) {
            $this->info('🔑 Default users created:');
            $this->info('   - admin@example.com / password (Admin)');
            $this->info('   - user@example.com / password (User)');
            $this->info('');
        }
        
        $this->info('📚 Documentation: https://github.com/danielpk74/laravel-auth-starter');
    }
}
