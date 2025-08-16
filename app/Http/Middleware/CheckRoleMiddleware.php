<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin has all permissions
        if ($user->role === 'Super Admin') {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If user doesn't have required role, redirect to dashboard with error
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}
