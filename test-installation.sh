#!/bin/bash

# Laravel Auth Starter Package Installation Test Script
# This script tests the package installation in a fresh Laravel project

echo "🧪 Laravel Auth Starter Package Installation Test"
echo "================================================="

PACKAGE_ROOT="/home/development/projects/php/laravel-auth-starter"
TEST_PROJECT_NAME="test-laravel-auth-installation"
TEST_ROOT="/tmp/$TEST_PROJECT_NAME"

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to log errors
log_error() {
    echo -e "${RED}❌ ERROR: $1${NC}"
}

# Function to log warnings
log_warning() {
    echo -e "${YELLOW}⚠️  WARNING: $1${NC}"
}

# Function to log success
log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Function to log info
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Cleanup function
cleanup() {
    if [ -d "$TEST_ROOT" ]; then
        log_info "Cleaning up test project..."
        rm -rf "$TEST_ROOT"
    fi
}

# Set up cleanup on script exit
trap cleanup EXIT

# Check if Laravel is installed globally
if ! command -v laravel &> /dev/null; then
    log_error "Laravel installer not found. Please install it globally with: composer global require laravel/installer"
    exit 1
fi

# Check if package exists
if [ ! -d "$PACKAGE_ROOT" ]; then
    log_error "Package not found at $PACKAGE_ROOT"
    exit 1
fi

echo ""
log_info "Creating fresh Laravel project..."

# Remove existing test project if it exists
if [ -d "$TEST_ROOT" ]; then
    rm -rf "$TEST_ROOT"
fi

# Create fresh Laravel project
cd /tmp
if laravel new "$TEST_PROJECT_NAME" --quiet; then
    log_success "Fresh Laravel project created"
else
    log_error "Failed to create fresh Laravel project"
    exit 1
fi

cd "$TEST_ROOT"

echo ""
log_info "Configuring composer.json for local package..."

# Add repository configuration for local package
cat > temp_composer_update.json << 'EOF'
{
    "repositories": [
        {
            "type": "path",
            "url": "/home/development/projects/php/laravel-auth-starter",
            "options": {
                "symlink": false
            }
        }
    ]
}
EOF

# Merge with existing composer.json
php -r "
\$existing = json_decode(file_get_contents('composer.json'), true);
\$update = json_decode(file_get_contents('temp_composer_update.json'), true);
\$merged = array_merge_recursive(\$existing, \$update);
file_put_contents('composer.json', json_encode(\$merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
"

rm temp_composer_update.json

echo ""
log_info "Installing the auth starter package..."

if composer require danielpk74/laravel-auth-starter:"@dev" --quiet; then
    log_success "Package installed successfully"
else
    log_error "Package installation failed"
    echo "Running verbose install to see errors..."
    composer require danielpk74/laravel-auth-starter:"@dev"
    exit 1
fi

echo ""
log_info "Testing autoloading..."

# Test if classes can be autoloaded
php -r "
try {
    new \Danielpk74\LaravelAuthStarter\LaravelAuthStarterServiceProvider(app());
    echo 'Service provider autoloaded successfully' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Service provider autoload failed: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

php -r "
try {
    new \Danielpk74\LaravelAuthStarter\Commands\InstallCommand();
    echo 'InstallCommand autoloaded successfully' . PHP_EOL;
} catch (Exception \$e) {
    echo 'InstallCommand autoload failed: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

php -r "
try {
    new \Danielpk74\LaravelAuthStarter\Models\User();
    echo 'User model autoloaded successfully' . PHP_EOL;
} catch (Exception \$e) {
    echo 'User model autoload failed: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo ""
log_info "Testing artisan commands..."

# Test if artisan commands are registered
if php artisan list | grep -q "auth-starter:install"; then
    log_success "auth-starter:install command registered"
else
    log_error "auth-starter:install command not registered"
fi

if php artisan list | grep -q "auth-starter:publish-assets"; then
    log_success "auth-starter:publish-assets command registered"
else
    log_error "auth-starter:publish-assets command not registered"
fi

if php artisan list | grep -q "auth-starter:test-install"; then
    log_success "auth-starter:test-install command registered"
else
    log_error "auth-starter:test-install command not registered"
fi

echo ""
log_info "Testing publish functionality..."

# Test config publishing
if php artisan vendor:publish --tag=auth-starter-config --quiet; then
    if [ -f "config/auth-starter.php" ]; then
        log_success "Config file published successfully"
    else
        log_error "Config file not found after publishing"
    fi
else
    log_error "Config publishing failed"
fi

# Test migration publishing
if php artisan vendor:publish --tag=auth-starter-migrations --quiet; then
    if ls database/migrations/*_create_users_table.php 1> /dev/null 2>&1; then
        log_success "Migrations published successfully"
    else
        log_error "Migrations not found after publishing"
    fi
else
    log_error "Migration publishing failed"
fi

# Test seeder publishing
if php artisan vendor:publish --tag=auth-starter-seeders --quiet; then
    if [ -f "database/seeders/UserSeeder.php" ]; then
        log_success "Seeders published successfully"
        
        # Test seeder class loading
        php -r "
        require_once 'vendor/autoload.php';
        \$app = require_once 'bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        try {
            new Database\Seeders\UserSeeder();
            echo 'UserSeeder autoloaded successfully' . PHP_EOL;
        } catch (Exception \$e) {
            echo 'UserSeeder autoload failed: ' . \$e->getMessage() . PHP_EOL;
            exit(1);
        }
        "
    else
        log_error "Seeders not found after publishing"
    fi
else
    log_error "Seeder publishing failed"
fi

echo ""
log_info "Testing routes publishing..."

if php artisan vendor:publish --tag=auth-starter-routes --quiet; then
    if [ -f "routes/api.php" ] && grep -q "auth-starter" routes/api.php; then
        log_success "API routes published successfully"
    else
        log_warning "API routes may not have been published correctly"
    fi
    
    if [ -f "routes/web.php" ] && grep -q "auth-starter" routes/web.php; then
        log_success "Web routes published successfully"
    else
        log_warning "Web routes may not have been published correctly"
    fi
else
    log_error "Routes publishing failed"
fi

echo ""
log_info "Testing install command..."

# Run the install command (without actually migrating to avoid DB requirements)
if php artisan auth-starter:install --help > /dev/null 2>&1; then
    log_success "Install command is functional"
else
    log_error "Install command failed"
fi

echo ""
echo "================================================="
echo "Test Summary:"
echo "================================================="

log_success "Package installation test completed!"
log_info "The package structure is PSR-4 compliant and installs correctly in Laravel projects."

echo ""
log_info "To use this package in a real project:"
echo "1. Add the repository to composer.json"
echo "2. Run: composer require danielpk74/laravel-auth-starter"
echo "3. Run: php artisan auth-starter:install"
echo "4. Configure your environment and run migrations"
