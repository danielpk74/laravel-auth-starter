<?php

use Illuminate\Support\Facades\Route;
use Danielpk74\LaravelAuthStarter\Http\Controllers\Auth\AuthController;
use Danielpk74\LaravelAuthStarter\Http\Controllers\Admin\UserController;

// Public authentication routes
Route::prefix(config('auth-starter.routes.api_prefix', 'api/auth'))
    ->middleware(config('auth-starter.routes.api_middleware', ['api']))
    ->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        
        if (config('auth-starter.features.registration', true)) {
            Route::post('/register', [AuthController::class, 'register']);
        }
    });

// Protected authentication routes
Route::prefix(config('auth-starter.routes.api_prefix', 'api/auth'))
    ->middleware(array_merge(
        config('auth-starter.routes.api_middleware', ['api']),
        config('auth-starter.routes.protected_middleware', ['auth:sanctum'])
    ))
    ->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        if (config('auth-starter.features.profile_management', true)) {
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'changePassword']);
        }
        
        if (config('auth-starter.tokens.refresh_enabled', true)) {
            Route::post('/refresh', [AuthController::class, 'refresh']);
        }
    });

// Admin routes for user management
if (config('auth-starter.features.role_management', true)) {
    Route::prefix('api/admin')
        ->middleware(array_merge(
            config('auth-starter.routes.api_middleware', ['api']),
            config('auth-starter.routes.protected_middleware', ['auth:sanctum'])
        ))
        ->group(function () {
            Route::apiResource('users', UserController::class);
            Route::put('users/{id}/role', [UserController::class, 'changeRole']);
            Route::get('users/search', [UserController::class, 'searchUser']);
            Route::delete('users/bulk', [UserController::class, 'deleteUserBulk']);
        });
}
