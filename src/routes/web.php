<?php

use Illuminate\Support\Facades\Route;

// Web routes for the package
// These can be customized based on your frontend needs

Route::prefix(config('auth-starter.routes.web_prefix', 'auth'))
    ->middleware(config('auth-starter.routes.web_middleware', ['web']))
    ->group(function () {
        // Add any web routes you need for the authentication system
        // For example, password reset pages, email verification, etc.
    });
