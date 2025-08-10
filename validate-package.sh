#!/bin/bash

# Laravel Auth Starter Package Validation Script
# This script validates the package structure and fixes common PSR-4 autoloading issues

echo "🔍 Laravel Auth Starter Package Validation Script"
echo "=================================================="

PACKAGE_ROOT="/home/development/projects/php/laravel-auth-starter"
ERRORS=0
WARNINGS=0

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to log errors
log_error() {
    echo -e "${RED}❌ ERROR: $1${NC}"
    ((ERRORS++))
}

# Function to log warnings
log_warning() {
    echo -e "${YELLOW}⚠️  WARNING: $1${NC}"
    ((WARNINGS++))
}

# Function to log success
log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Function to log info
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

echo ""
log_info "Validating package structure..."

# Check if we're in the right directory
if [ ! -f "$PACKAGE_ROOT/composer.json" ]; then
    log_error "composer.json not found in $PACKAGE_ROOT"
    exit 1
fi

cd "$PACKAGE_ROOT"

# Validate composer.json
echo ""
log_info "Checking composer.json validity..."
if composer validate --quiet; then
    log_success "composer.json is valid"
else
    log_error "composer.json is invalid"
    composer validate
fi

# Check PSR-4 autoloading structure
echo ""
log_info "Checking PSR-4 autoloading structure..."

# Check if src directory exists
if [ ! -d "src" ]; then
    log_error "src/ directory not found"
else
    log_success "src/ directory exists"
fi

# Check for incorrectly placed directories in root
INCORRECT_DIRS=("app" "config" "routes" "database")
for dir in "${INCORRECT_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        log_error "Found $dir/ directory in root - should be in src/"
    fi
done

# Validate directory structure follows PSR-4
echo ""
log_info "Validating directory structure..."

REQUIRED_DIRS=(
    "src/Commands"
    "src/Http/Controllers"
    "src/Http/Middleware"
    "src/Http/Requests"
    "src/Models"
    "src/Enums"
    "src/Database/Migrations"
    "src/Database/Seeders"
    "src/config"
    "src/routes"
    "src/resources"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        log_success "$dir exists"
    else
        log_warning "$dir not found"
    fi
done

# Check for old lowercase database directory
if [ -d "src/database" ]; then
    log_error "Found src/database/ (lowercase) - should be src/Database/ (capital D)"
fi

# Validate namespace consistency
echo ""
log_info "Checking namespace consistency..."

# Check PHP files for correct namespaces
find src -name "*.php" -exec grep -l "^namespace" {} \; | while read file; do
    # Get the expected namespace based on file path
    relative_path=$(echo "$file" | sed 's|^src/||' | sed 's|\.php$||' | sed 's|/|\\|g')
    expected_namespace="Danielpk74\\LaravelAuthStarter\\$relative_path"
    
    # Get actual namespace from file
    actual_namespace=$(grep "^namespace" "$file" | sed 's/namespace //' | sed 's/;//')
    
    # Compare expected vs actual (allowing for the class name difference)
    expected_dir=$(dirname "$expected_namespace" | sed 's|\\|/|g')
    actual_dir=$(dirname "$actual_namespace" | sed 's|\\|/|g')
    
    if [[ "$actual_dir" != "$expected_dir" ]]; then
        log_error "Namespace mismatch in $file"
        echo "  Expected: starts with Danielpk74\\LaravelAuthStarter\\$(dirname "$relative_path" | sed 's|/|\\|g')"
        echo "  Actual: $actual_namespace"
    fi
done

# Test autoloading
echo ""
log_info "Testing autoloading..."

if composer dump-autoload --optimize --quiet 2>/dev/null; then
    log_success "Autoloading generation successful"
else
    log_error "Autoloading generation failed"
    echo "Running verbose autoload to see errors..."
    composer dump-autoload --optimize
fi

# Check for service provider registration
echo ""
log_info "Checking service provider..."

SERVICE_PROVIDER="src/LaravelAuthStarterServiceProvider.php"
if [ -f "$SERVICE_PROVIDER" ]; then
    log_success "Service provider exists"
    
    # Check if all publish tags are properly configured
    TAGS=(
        "auth-starter-config"
        "auth-starter-migrations" 
        "auth-starter-seeders"
        "auth-starter-js"
        "auth-starter-components"
        "auth-starter-css"
        "auth-starter-locales"
        "auth-starter-views"
    )
    
    for tag in "${TAGS[@]}"; do
        if grep -q "$tag" "$SERVICE_PROVIDER"; then
            log_success "Publish tag '$tag' configured"
        else
            log_warning "Publish tag '$tag' not found in service provider"
        fi
    done
else
    log_error "Service provider not found"
fi

# Check for test structure
echo ""
log_info "Checking test structure..."

if [ -d "tests" ]; then
    log_success "tests/ directory exists"
    
    if [ -f "tests/TestCase.php" ]; then
        log_success "TestCase.php exists"
    else
        log_warning "TestCase.php not found in tests/"
    fi
    
    if [ -f "phpunit.xml" ]; then
        log_success "phpunit.xml exists"
    else
        log_warning "phpunit.xml not found"
    fi
else
    log_warning "tests/ directory not found"
fi

# Final summary
echo ""
echo "=================================================="
echo "Validation Summary:"
echo "=================================================="

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    log_success "Package structure is perfect! ✨"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}Package structure is good with $WARNINGS warnings${NC}"
    exit 0
else
    echo -e "${RED}Package structure has $ERRORS errors and $WARNINGS warnings${NC}"
    echo ""
    echo "Please fix the errors before publishing the package."
    exit 1
fi
