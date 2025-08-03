<?php

namespace Danielpk74\LaravelAuthStarter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            
            abort(401, 'Unauthenticated');
        }

        $user = auth()->user();
        
        // Get role configuration
        $roles = config('auth-starter.roles', [
            'admin' => 1,
            'user' => 2,
        ]);
        
        $requiredRoleValue = $roles[$role] ?? null;
        
        if ($requiredRoleValue === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid role specified',
                ], 403);
            }
            
            abort(403, 'Invalid role specified');
        }
        
        if (!isset($user->role) || $user->role !== $requiredRoleValue) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions',
                ], 403);
            }
            
            abort(403, 'Insufficient permissions');
        }

        return $next($request);
    }
}
