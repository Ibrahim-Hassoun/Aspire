<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class AdminOnly
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Check if user has admin privileges (id = 1)
        if (!$user->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.',
                'data' => [
                    'user_role' => $user->getRoleDisplayName(),
                    'required_role' => 'Admin',
                    'user_id' => $user->id
                ]
            ], 403);
        }

        return $next($request);
    }
}
