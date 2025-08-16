<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasRole')) {
    /**
     * Check if the current user has any of the given roles.
     *
     * @param  array|string  $roles
     * @return bool
     */
    function hasRole($roles)
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Super Admin has all roles
        if ($user->role === 'Super Admin') {
            return true;
        }
        
        if (is_string($roles)) {
            return $user->role === $roles;
        }
        
        return in_array($user->role, $roles);
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Check if the current user has any of the given roles.
     *
     * @param  array  $roles
     * @return bool
     */
    function hasAnyRole($roles)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return false;
            }
            
            // Super Admin has all roles
            if ($user->role === 'Super Admin') {
                return true;
            }
            
            return !empty(array_intersect((array)$roles, (array)$user->role));
        } catch (\Exception $e) {
            \Log::error('hasAnyRole check failed: ' . $e->getMessage());
            return false;
        }
    }
}
