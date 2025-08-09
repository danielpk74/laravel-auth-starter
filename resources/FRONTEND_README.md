# Laravel Auth Starter - Frontend Installation Guide

## 📦 Frontend Assets Included

This package includes complete Vue.js components and assets for:

- ✅ **Authentication Forms**: Login, Register, Forgot Password
- ✅ **Admin Components**: Dashboard, User Navigation
- ✅ **Auth Pages**: Profile management, password change
- ✅ **Pinia Store**: Authentication state management
- ✅ **Vue Router**: Route configuration
- ✅ **AdminLTE Integration**: Complete UI framework
- ✅ **Internationalization**: Multi-language support
- ✅ **Form Validation**: Vee-Validate with Yup schemas

## 🚀 Installation Options

### Option 1: Auto-Install (Recommended)

```bash
# Install package with frontend assets
php artisan auth-starter:install --with-frontend

# Install NPM dependencies
npm install
```

### Option 2: Manual Installation

```bash
# Publish frontend assets
php artisan auth-starter:publish js
php artisan auth-starter:publish css

# Install required NPM packages
npm install vue@^3.3.4 vue-router@^4.2.5 pinia@^2.1.7 admin-lte@^3.2.0 axios@^1.1.2 vee-validate@^4.11.8 vue-i18n@^9.13.1 yup@^1.3.2 sweetalert2@^11.6.13 toastr@^2.1.4
```

## 📁 Published Structure

After installation, you'll have:

```
resources/js/auth-starter/
├── app.js                    # Main entry point
├── bootstrap.js              # Axios/jQuery setup
├── routes.js                 # Vue Router routes
├── stores/
│   └── auth.js              # Pinia auth store
├── Pages/
│   ├── Auth/                # Auth pages
│   └── Profile/             # Profile pages
└── helpers/                 # Utility functions

resources/components/auth-starter/
├── auth/
│   ├── LoginForm.vue        # Login component
│   ├── RegisterForm.vue     # Registration component
│   └── ForgotPasswordForm.vue
└── admin/
    ├── Dashboard.vue        # Admin dashboard
    └── UserNav.vue          # User navigation

resources/css/auth-starter/
└── app.css                  # Main styles

resources/locales/
├── en.json                  # English translations
└── es.json                  # Spanish translations
```

## 🔧 Usage Examples

### Quick Start (Simple Setup)

```javascript
// resources/js/app.js
import { createAuthStarter } from './auth-starter/app.js';

const { app } = createAuthStarter();
app.mount('#app');
```

### Advanced Setup (Custom Configuration)

```javascript
// resources/js/app.js
import { createAuthStarter } from './auth-starter/app.js';

const { app, router, components, stores } = createAuthStarter({
    locale: 'en',
    routes: [
        // Your custom routes
        { path: '/custom', component: CustomComponent }
    ],
    messages: {
        en: { custom: 'Custom message' },
        es: { custom: 'Mensaje personalizado' }
    }
});

// Add custom components
app.component('CustomComponent', CustomComponent);

app.mount('#app');
```

### Individual Component Usage

```vue
<template>
  <div>
    <!-- Use auth components anywhere -->
    <LoginForm @login-success="handleLogin" />
    <RegisterForm @register-success="handleRegister" />
    <UserNav :user="currentUser" />
  </div>
</template>

<script setup>
import { LoginForm, RegisterForm, UserNav } from './auth-starter/app.js';
import { useAuthStore } from './auth-starter/stores/auth.js';

const authStore = useAuthStore();
const currentUser = computed(() => authStore.user);

const handleLogin = (userData) => {
  console.log('User logged in:', userData);
};

const handleRegister = (userData) => {
  console.log('User registered:', userData);
};
</script>
```

## ⚙️ Configuration

### Vite Configuration

Add to your `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                // Auth starter assets
                'resources/js/auth-starter/app.js',
                'resources/css/auth-starter/app.css',
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@auth-starter': '/resources/js/auth-starter',
        },
    },
});
```

### Blade Template Integration

```blade
{{-- resources/views/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Auth Starter</title>
    @vite(['resources/js/auth-starter/app.js', 'resources/css/auth-starter/app.css'])
</head>
<body class="hold-transition sidebar-mini">
    <div id="app"></div>
</body>
</html>
```

## 🎨 Customization

### Override Components

```javascript
// Create your custom login form
// resources/js/components/CustomLoginForm.vue
<template>
  <div class="my-custom-login">
    <!-- Your custom login form -->
  </div>
</template>

// Register in your app
import CustomLoginForm from './components/CustomLoginForm.vue';
app.component('LoginForm', CustomLoginForm); // Override default
```

### Extend Auth Store

```javascript
// resources/js/stores/authExtended.js
import { useAuthStore } from './auth-starter/stores/auth.js';

export const useExtendedAuthStore = () => {
  const authStore = useAuthStore();
  
  // Add custom methods
  const customLogin = async (credentials) => {
    await authStore.login(credentials);
    // Your custom logic after login
  };
  
  return {
    ...authStore,
    customLogin,
  };
};
```

## 🌐 Internationalization

### Add New Language

```javascript
// resources/locales/fr.json
{
  "auth": {
    "login": "Connexion",
    "email": "Email",
    "password": "Mot de passe"
  }
}

// Add to app configuration
const { app } = createAuthStarter({
  locale: 'fr',
  messages: {
    fr: frMessages
  }
});
```

## 🔌 Available Components

| Component | Description | Props |
|-----------|-------------|-------|
| `LoginForm` | Login form with validation | `@login-success`, `@login-error` |
| `RegisterForm` | Registration form | `@register-success`, `@register-error` |
| `ForgotPasswordForm` | Password reset form | `@reset-success` |
| `UserNav` | User navigation menu | `:user` |
| `Dashboard` | Admin dashboard | `:stats` |

## 🏪 Pinia Store (Auth)

```javascript
import { useAuthStore } from './auth-starter/stores/auth.js';

const authStore = useAuthStore();

// Available methods
await authStore.login(credentials);
await authStore.register(userData);
await authStore.logout();
await authStore.updateProfile(profileData);
await authStore.changePassword(passwordData);

// Available getters
authStore.isAuthenticated;
authStore.user;
authStore.token;
authStore.isAdmin;
authStore.isUser;
```

## 🎯 Next Steps

1. Customize the components to match your design
2. Add your business logic to the auth store
3. Configure your routes and navigation
4. Add custom validation rules if needed
5. Set up your preferred CSS framework alongside AdminLTE

The frontend package provides a complete authentication UI that works seamlessly with the Laravel backend!
