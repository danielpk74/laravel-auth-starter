#!/bin/bash

# Script to update the document-management project to use the laravel-auth-starter package
PROJECT_PATH="/home/development/projects/php/document-management"

echo "🚀 Updating document-management project to use laravel-auth-starter package..."

# Check if the project exists
if [ ! -d "$PROJECT_PATH" ]; then
    echo "❌ Project directory not found: $PROJECT_PATH"
    exit 1
fi

# Navigate to the project
cd "$PROJECT_PATH"

echo "📍 Working in: $(pwd)"

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json not found in the project directory"
    exit 1
fi

echo "🔄 Clearing Composer cache..."
composer clear-cache

echo "📝 Adding VCS repository configuration..."
# Add the VCS repository if it doesn't exist
if ! grep -q "danielpk74/laravel-auth-starter" composer.json; then
    # Use PHP to properly update composer.json
    php -r "
    \$composer = json_decode(file_get_contents('composer.json'), true);
    
    // Add repository if not exists
    if (!isset(\$composer['repositories'])) {
        \$composer['repositories'] = [];
    }
    
    // Check if repository already exists
    \$repoExists = false;
    foreach (\$composer['repositories'] as \$repo) {
        if (isset(\$repo['url']) && \$repo['url'] === 'https://github.com/danielpk74/laravel-auth-starter') {
            \$repoExists = true;
            break;
        }
    }
    
    if (!\$repoExists) {
        \$composer['repositories'][] = [
            'type' => 'vcs',
            'url' => 'https://github.com/danielpk74/laravel-auth-starter'
        ];
    }
    
    // Update the package requirement
    \$composer['require']['danielpk74/laravel-auth-starter'] = '^1.0';
    
    file_put_contents('composer.json', json_encode(\$composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo 'composer.json updated successfully\n';
    "
else
    echo "📦 Repository already configured, updating version..."
    composer require danielpk74/laravel-auth-starter:^1.0 --no-update
fi

echo "📦 Installing/updating dependencies..."
composer update danielpk74/laravel-auth-starter

echo "✅ Package installation completed!"
echo ""
echo "🎯 Next steps:"
echo "   1. Run: php artisan auth-starter:install"
echo "   2. Check the package documentation for configuration options"
echo ""
echo "🔗 Package repository: https://github.com/danielpk74/laravel-auth-starter"
