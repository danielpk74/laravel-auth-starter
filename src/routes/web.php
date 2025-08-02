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


// Authentication routes
Route::prefix('api/v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('auth.user');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    });
});

Route::prefix('api')->group(function () {
    Route::prefix('/v1')->middleware(['auth:sanctum'])->group(function () {

        Route::prefix('/users')->middleware(['role:admin'])->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/search', [UserController::class, 'searchUser']);
            Route::post('/', [UserController::class, 'store']);
            Route::patch('/{user}/change-role', [UserController::class, 'changeRole']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
            Route::delete('/', [UserController::class, 'deleteUserBulk']);
        });

    });
});
