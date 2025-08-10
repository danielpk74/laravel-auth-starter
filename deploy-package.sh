#!/bin/bash

# Laravel Auth Starter Package Deployment Script
# This script prepares the package for release by validating, testing, and tagging

echo "🚀 Laravel Auth Starter Package Deployment Script"
echo "================================================="

PACKAGE_ROOT="/home/development/projects/php/laravel-auth-starter"
CURRENT_VERSION=""

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
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

# Function to log step
log_step() {
    echo -e "${PURPLE}🔄 $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "$PACKAGE_ROOT/composer.json" ]; then
    log_error "composer.json not found in $PACKAGE_ROOT"
    exit 1
fi

cd "$PACKAGE_ROOT"

# Check if git is initialized
if [ ! -d ".git" ]; then
    log_error "Git repository not initialized. Please run 'git init' first."
    exit 1
fi

# Get current version from composer.json
CURRENT_VERSION=$(php -r "echo json_decode(file_get_contents('composer.json'), true)['version'] ?? 'dev';" 2>/dev/null)

echo ""
log_info "Current package version: $CURRENT_VERSION"

# Step 1: Validate package structure
echo ""
log_step "Step 1: Validating package structure..."

if ./validate-package.sh > /dev/null 2>&1; then
    log_success "Package structure validation passed"
else
    log_error "Package structure validation failed. Please fix issues first."
    echo ""
    ./validate-package.sh
    exit 1
fi

# Step 2: Run composer validation and autoload
echo ""
log_step "Step 2: Running composer validation..."

if composer validate --quiet; then
    log_success "Composer validation passed"
else
    log_error "Composer validation failed"
    composer validate
    exit 1
fi

log_info "Optimizing autoloader..."
if composer dump-autoload --optimize --quiet; then
    log_success "Autoloader optimization complete"
else
    log_error "Autoloader optimization failed"
    exit 1
fi

# Step 3: Run tests if they exist
echo ""
log_step "Step 3: Running tests..."

if [ -f "phpunit.xml" ] && [ -d "tests" ]; then
    if composer test > /dev/null 2>&1; then
        log_success "All tests passed"
    else
        log_warning "Some tests failed. Please review test results."
        echo ""
        log_info "Running tests with output..."
        composer test
        echo ""
        read -p "Continue deployment despite test failures? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "Deployment cancelled"
            exit 1
        fi
    fi
else
    log_info "No tests found, skipping test execution"
fi

# Step 4: Check git status
echo ""
log_step "Step 4: Checking git status..."

if [ -n "$(git status --porcelain)" ]; then
    log_info "Uncommitted changes found:"
    git status --short
    echo ""
    read -p "Commit changes before deployment? (Y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Nn]$ ]]; then
        read -p "Enter commit message: " commit_message
        git add .
        git commit -m "${commit_message:-"Prepare package for release"}"
        log_success "Changes committed"
    fi
else
    log_success "Working directory is clean"
fi

# Step 5: Version management
echo ""
log_step "Step 5: Version management..."

echo "Current version: $CURRENT_VERSION"
echo ""
echo "Version options:"
echo "1. Patch (x.x.X) - Bug fixes"
echo "2. Minor (x.X.x) - New features, backward compatible"
echo "3. Major (X.x.x) - Breaking changes"
echo "4. Custom version"
echo "5. Skip versioning"
echo ""

read -p "Select version increment (1-5): " version_choice

case $version_choice in
    1)
        NEW_VERSION=$(php -r "
        \$v = '$CURRENT_VERSION';
        if (\$v === 'dev') \$v = '1.0.0';
        \$parts = explode('.', \$v);
        \$parts[2] = (int)\$parts[2] + 1;
        echo implode('.', \$parts);
        ")
        ;;
    2)
        NEW_VERSION=$(php -r "
        \$v = '$CURRENT_VERSION';
        if (\$v === 'dev') \$v = '1.0.0';
        \$parts = explode('.', \$v);
        \$parts[1] = (int)\$parts[1] + 1;
        \$parts[2] = 0;
        echo implode('.', \$parts);
        ")
        ;;
    3)
        NEW_VERSION=$(php -r "
        \$v = '$CURRENT_VERSION';
        if (\$v === 'dev') \$v = '1.0.0';
        \$parts = explode('.', \$v);
        \$parts[0] = (int)\$parts[0] + 1;
        \$parts[1] = 0;
        \$parts[2] = 0;
        echo implode('.', \$parts);
        ")
        ;;
    4)
        read -p "Enter custom version: " NEW_VERSION
        ;;
    5)
        NEW_VERSION=$CURRENT_VERSION
        log_info "Skipping version update"
        ;;
    *)
        log_error "Invalid choice"
        exit 1
        ;;
esac

if [ "$NEW_VERSION" != "$CURRENT_VERSION" ] && [ -n "$NEW_VERSION" ]; then
    log_info "Updating version from $CURRENT_VERSION to $NEW_VERSION"
    
    # Update composer.json version
    php -r "
    \$composer = json_decode(file_get_contents('composer.json'), true);
    \$composer['version'] = '$NEW_VERSION';
    file_put_contents('composer.json', json_encode(\$composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    "
    
    log_success "Version updated to $NEW_VERSION"
    
    # Commit version update
    git add composer.json
    git commit -m "Bump version to $NEW_VERSION"
fi

# Step 6: Create git tag
echo ""
log_step "Step 6: Creating git tag..."

if [ "$NEW_VERSION" != "dev" ] && [ -n "$NEW_VERSION" ]; then
    TAG_NAME="v$NEW_VERSION"
    
    # Check if tag already exists
    if git tag -l | grep -q "^$TAG_NAME$"; then
        log_warning "Tag $TAG_NAME already exists"
        read -p "Delete existing tag and create new one? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git tag -d "$TAG_NAME"
            log_info "Deleted existing tag $TAG_NAME"
        else
            log_info "Skipping tag creation"
            TAG_NAME=""
        fi
    fi
    
    if [ -n "$TAG_NAME" ]; then
        git tag -a "$TAG_NAME" -m "Release version $NEW_VERSION"
        log_success "Created tag $TAG_NAME"
    fi
else
    log_info "Skipping tag creation (dev version)"
fi

# Step 7: Generate deployment summary
echo ""
log_step "Step 7: Deployment summary..."

echo ""
echo "================================================="
echo "🎉 Deployment Summary"
echo "================================================="
log_success "Package validation: PASSED"
log_success "Composer validation: PASSED"
log_success "Autoloader optimization: COMPLETE"

if [ -f "phpunit.xml" ]; then
    log_success "Tests: EXECUTED"
else
    log_info "Tests: SKIPPED (no tests found)"
fi

if [ "$NEW_VERSION" != "$CURRENT_VERSION" ]; then
    log_success "Version: UPDATED ($CURRENT_VERSION → $NEW_VERSION)"
else
    log_info "Version: UNCHANGED ($CURRENT_VERSION)"
fi

if [ -n "$TAG_NAME" ]; then
    log_success "Git tag: CREATED ($TAG_NAME)"
else
    log_info "Git tag: SKIPPED"
fi

echo ""
echo "📦 Package is ready for distribution!"
echo ""
log_info "Next steps:"
echo "1. Push to remote repository: git push && git push --tags"
echo "2. Publish to Packagist if not already published"
echo "3. Update documentation if needed"
echo ""
log_info "To install in a Laravel project:"
echo "composer require danielpk74/laravel-auth-starter"
echo "php artisan auth-starter:install"
