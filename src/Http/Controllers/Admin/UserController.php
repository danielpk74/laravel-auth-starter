<?php

namespace Danielpk74\LaravelAuthStarter\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class UserController
{
    public function index()
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'User management is disabled',
            ], 403);
        }

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        return $userModel::latest()->paginate(10);
    }

    public function store(Request $request): JsonResponse
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'User management is disabled',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(array_values(RoleType::toArray()))],
        ]);

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $user = $userModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'User management is disabled',
            ], 403);
        }

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $user = $userModel::findOrFail($id);

        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['sometimes', Rule::in(array_values(RoleType::toArray()))],
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8';
        }
        
        $request->validate($validationRules);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('role')) {
            $data['role'] = $request->role;
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'User management is disabled',
            ], 403);
        }

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $user = $userModel::findOrFail($id);
        
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function changeRole(Request $request, $id): JsonResponse
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Role management is disabled',
            ], 403);
        }

        $request->validate([
            'role' => ['required', Rule::in(array_values(RoleType::toArray()))],
        ]);

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $user = $userModel::findOrFail($id);

        $user->update([
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully',
            'data' => $user,
        ]);
    }

    public function searchUser(Request $request): JsonResponse
    {
        $searchUserQuery = $request->get('query');
        
        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $users = $userModel::where('name', 'like', "%{$searchUserQuery}%")
                          ->orWhere('email', 'like', "%{$searchUserQuery}%")
                          ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function deleteUserBulk(Request $request): JsonResponse
    {
        if (!config('auth-starter.features.role_management', true)) {
            return response()->json([
                'success' => false,
                'message' => 'User management is disabled',
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        $userModel::whereIn('id', $request->get('ids'))->delete();

        return response()->json([
            'success' => true,
            'message' => 'Users deleted successfully',
        ]);
    }
}
