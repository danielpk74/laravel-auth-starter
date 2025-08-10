<?php

namespace Danielpk74\LaravelAuthStarter\Commands;

use Illuminate\Console\Command;

class PublishAssetsCommand extends Command
{
    protected $signature = 'auth-starter:publish 
                            {type? : Type of assets to publish (config|migrations|js|components|css|locales|all)}
                            {--force : Overwrite existing files}';

    protected $description = 'Publish Laravel Auth Starter assets';

    public function handle()
    {
        $type = $this->argument('type') ?? 'all';

        switch ($type) {
            case 'config':
                $this->publishConfig();
                break;
            case 'migrations':
                $this->publishMigrations();
                break;
            case 'js':
                $this->publishJavaScript();
                break;
            case 'components':
                $this->publishComponents();
                break;
            case 'css':
                $this->publishCSS();
                break;
            case 'locales':
                $this->publishLocales();
                break;
            case 'all':
                $this->publishAll();
                break;
            default:
                $this->error('Invalid type. Use: config, migrations, js, components, css, locales, or all');
                return 1;
        }

        return 0;
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

    protected function publishJavaScript()
    {
        $this->info('📝 Publishing JavaScript assets...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-js',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishComponents()
    {
        $this->info('📝 Publishing Vue components...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-components',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishCSS()
    {
        $this->info('📝 Publishing CSS assets...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-css',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishLocales()
    {
        $this->info('📝 Publishing language files...');
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-locales',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishAll()
    {
        $this->info('📝 Publishing all assets...');
        
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishJavaScript();
        $this->publishComponents();
        $this->publishCSS();
        $this->publishLocales();
        
        $this->call('vendor:publish', [
            '--tag' => 'auth-starter-config-files',
            '--force' => $this->option('force'),
        ]);
        
        $this->info('✅ All assets published successfully!');
        $this->warn('💡 Run "npm install" to install frontend dependencies');
    }
}
