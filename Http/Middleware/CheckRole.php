<?php

namespace Danielpk74\LaravelAuthStarter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $userRole = $request->user()->getRawOriginal('role');
        
        // Convert role names to values for comparison
        $allowedRoles = array_map(function ($role) {
            return match (strtolower($role)) {
                'admin' => RoleType::Admin->value,
                'user' => RoleType::User->value,
                default => null,
            };
        }, $roles);

        // Remove null values
        $allowedRoles = array_filter($allowedRoles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}
