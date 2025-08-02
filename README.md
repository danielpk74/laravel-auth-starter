# Laravel Auth Starter

A comprehensive Laravel authentication and user management starter package with Vue.js frontend, AdminLTE integration, and role-based access control.

## Features

- 🔐 **Complete Authentication System**: Login, registration, profile management, password reset
- 👥 **Role-Based Access Control**: Admin and User roles with flexible permissions
- 🎨 **Vue.js Frontend**: Pre-built components with AdminLTE integration
- 🛡️ **Laravel Sanctum**: API token authentication
- ⚙️ **Highly Configurable**: Extensive configuration options
- 🚀 **Easy Installation**: One-command setup
- 📱 **API Ready**: RESTful API endpoints for all authentication features
- 🎯 **Modern Stack**: Laravel 10+, Vue 3, Vite, Pinia

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Node.js & NPM (for frontend assets)

## Installation

1. Install the package via Composer:

```bash
composer require danielpk74/laravel-auth-starter
```

2. Install the package:

```bash
php artisan auth-starter:install --with-frontend --with-seeders
```

3. Install frontend dependencies (if using frontend assets):

```bash
npm install
```

## Quick Start

After installation, you'll have access to these API endpoints:

### Authentication Endpoints

```bash
# Register a new user
POST /api/auth/register

# Login
POST /api/auth/login

# Get authenticated user (requires token)
GET /api/auth/user

# Update profile (requires token)
PUT /api/auth/profile

# Change password (requires token)
PUT /api/auth/password

# Logout (requires token)
POST /api/auth/logout

# Refresh token (requires token)
POST /api/auth/refresh
```

### Admin Endpoints

```bash
# User management (requires admin role)
GET /api/admin/users
POST /api/admin/users
PUT /api/admin/users/{id}
DELETE /api/admin/users/{id}
PUT /api/admin/users/{id}/role
```

### Default Users

If you installed with `--with-seeders`, these users are available:

- **Admin**: admin@example.com / password
- **User**: user@example.com / password

## Configuration

The package is highly configurable via `config/auth-starter.php`:

```php
return [
    'models' => [
        'user' => \App\Models\User::class,
    ],
    'routes' => [
        'api_prefix' => 'api/auth',
        'web_prefix' => 'auth',
        // ... more options
    ],
    'features' => [
        'registration' => true,
        'password_reset' => true,
        'email_verification' => false,
        'profile_management' => true,
        'role_management' => true,
    ],
    // ... more configuration
];
```

## Frontend Integration

### Vue.js Components

The package includes pre-built Vue.js components:

```javascript
import { createApp } from 'vue'
import AuthLogin from './auth-starter/components/AuthLogin.vue'
import AuthRegister from './auth-starter/components/AuthRegister.vue'
import UserManagement from './auth-starter/components/UserManagement.vue'

const app = createApp({})
app.component('AuthLogin', AuthLogin)
app.component('AuthRegister', AuthRegister)
app.component('UserManagement', UserManagement)
```

### Pinia Store

```javascript
import { useAuthStore } from './auth-starter/stores/auth'

// In your component
const authStore = useAuthStore()
await authStore.login(credentials)
```

## User Model Integration

The package provides a `HasRoles` trait that extends your User model:

```php
use Danielpk74\LaravelAuthStarter\Models\Traits\HasRoles;
use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    
    // Your model code...
}

// Usage
$user->isAdmin(); // boolean
$user->hasRole(RoleType::Admin); // boolean
$user->assignRole(RoleType::User);
```

## Customization

### Publishing Assets

You can publish specific parts of the package:

```bash
# Publish configuration only
php artisan auth-starter:publish config

# Publish migrations only
php artisan auth-starter:publish migrations

# Publish JavaScript components
php artisan auth-starter:publish js

# Publish CSS files
php artisan auth-starter:publish css

# Publish everything
php artisan auth-starter:publish all
```

### Custom Controllers

You can extend or override the package controllers:

```php
use Danielpk74\LaravelAuthStarter\Http\Controllers\Auth\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{
    public function login(LoginRequest $request): JsonResponse
    {
        // Your custom logic
        return parent::login($request);
    }
}
```

## API Usage Examples

### Registration

```javascript
const response = await fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        password_confirmation: 'password123'
    })
});

const data = await response.json();
const token = data.data.token;
```

### Login

```javascript
const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        email: 'john@example.com',
        password: 'password123',
        remember: true
    })
});

const data = await response.json();
localStorage.setItem('token', data.data.token);
```

### Authenticated Requests

```javascript
const token = localStorage.getItem('token');

const response = await fetch('/api/auth/user', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    }
});

const userData = await response.json();
```

## Testing

Run the package tests:

```bash
composer test
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

- 📧 Email: danielpk74@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/danielpk74/laravel-auth-starter/issues)
- 📖 Documentation: [GitHub Wiki](https://github.com/danielpk74/laravel-auth-starter/wiki)
