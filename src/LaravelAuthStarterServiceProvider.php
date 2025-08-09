<?php

namespace Danielpk74\LaravelAuthStarter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Danielpk74\LaravelAuthStarter\Commands\InstallCommand;
use Danielpk74\LaravelAuthStarter\Commands\PublishAssetsCommand;
use Danielpk74\LaravelAuthStarter\Commands\TestInstallCommand;

class LaravelAuthStarterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/auth-starter.php', 'auth-starter'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/config/auth-starter.php' => config_path('auth-starter.php'),
        ], 'auth-starter-config');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'auth-starter-migrations');

        $this->publishes([
            __DIR__.'/Database/Seeders' => database_path('seeders'),
        ], 'auth-starter-seeders');

        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js/auth-starter'),
        ], 'auth-starter-js');

        $this->publishes([
            __DIR__.'/resources/components' => resource_path('components/auth-starter'),
        ], 'auth-starter-components');

        $this->publishes([
            __DIR__.'/resources/css' => resource_path('css/auth-starter'),
        ], 'auth-starter-css');

        $this->publishes([
            __DIR__.'/resources/locales' => resource_path('locales'),
        ], 'auth-starter-locales');

        $this->publishes([
            __DIR__.'/resources/package.json' => base_path('auth-starter-package.json'),
            __DIR__.'/resources/vite.config.js' => base_path('auth-starter-vite.config.js'),
            __DIR__.'/resources/FRONTEND_README.md' => base_path('AUTH_STARTER_FRONTEND.md'),
        ], 'auth-starter-config-files');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishAssetsCommand::class,
                TestInstallCommand::class,
            ]);
        }
    }
}
