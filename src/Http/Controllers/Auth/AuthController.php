<?php

namespace Danielpk74\LaravelAuthStarter\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Danielpk74\LaravelAuthStarter\Http\Requests\Auth\LoginRequest;
use Danielpk74\LaravelAuthStarter\Http\Requests\Auth\RegisterRequest;
use Danielpk74\LaravelAuthStarter\Http\Requests\Auth\UpdateProfileRequest;
use Danielpk74\LaravelAuthStarter\Http\Requests\Auth\ChangePasswordRequest;
use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class AuthController
{
    /**
     * Handle user login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember', false);

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $tokenName = config('auth-starter.tokens.name', 'auth-token');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        if (!config('auth-starter.features.registration', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Registration is disabled',
            ], 403);
        }

        $data = $request->validated();
        
        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $user = $userModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => RoleType::User->value, // Default to User role
        ]);

        $tokenName = config('auth-starter.tokens.name', 'auth-token');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        if (!config('auth-starter.features.profile_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Profile management is disabled',
            ], 403);
        }

        $user = $request->user();
        $data = $request->validated();

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Refresh user token
     */
    public function refresh(Request $request): JsonResponse
    {
        if (!config('auth-starter.tokens.refresh_enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh is disabled',
            ], 403);
        }

        $user = $request->user();
        
        // Delete current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $tokenName = config('auth-starter.tokens.name', 'auth-token');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
