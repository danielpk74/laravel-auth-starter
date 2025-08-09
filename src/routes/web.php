<?php

use Illuminate\Support\Facades\Route;
use Danielpk74\LaravelAuthStarter\Http\Controllers\ApplicationController;
use Danielpk74\LaravelAuthStarter\Http\Controllers\Admin\UserController;
use Danielpk74\LaravelAuthStarter\Http\Controllers\Auth\AuthController;
use Danielpk74\LaravelAuthStarter\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Health check route for Docker
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Health check route
Route::get('/health', HealthController::class);

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

Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
