<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify which models to use for the authentication system.
    | You can override these to use your own custom models.
    |
    */
    'models' => [
        'user' => \Danielpk74\LaravelAuthStarter\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routing behavior for the authentication system.
    |
    */
    'routes' => [
        'api_prefix' => 'api/auth',
        'web_prefix' => 'auth',
        'api_middleware' => ['api'],
        'web_middleware' => ['web'],
        'protected_middleware' => ['auth:sanctum'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles Configuration
    |--------------------------------------------------------------------------
    |
    | Define the available roles in your application.
    |
    */
    'roles' => [
        'admin' => 1,
        'user' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the authentication system.
    |
    */
    'features' => [
        'registration' => true,
        'password_reset' => true,
        'email_verification' => false,
        'profile_management' => true,
        'role_management' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the frontend integration settings.
    |
    */
    'frontend' => [
        'enabled' => true,
        'framework' => 'vue', // vue, react, angular
        'ui_framework' => 'adminlte', // adminlte, bootstrap, tailwind
        'publish_assets' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Configuration
    |--------------------------------------------------------------------------
    |
    | Configure token behavior for API authentication.
    |
    */
    'tokens' => [
        'name' => 'auth-token',
        'expiration' => null, // null for no expiration, or number of minutes
        'refresh_enabled' => true,
    ],
];
