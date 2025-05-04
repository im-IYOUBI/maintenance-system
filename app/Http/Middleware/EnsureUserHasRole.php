<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            // Not logged in - redirect to login
            return redirect()->route('login');
        }

        $roles = is_array($role) ? $role : explode('|', $role);
        
        if (!auth()->user()->hasAnyRole($roles)) {
            // User doesn't have required role
            if (auth()->user()->hasRole('admin')) {
                return redirect('/admin/dashboard');
            } elseif (auth()->user()->hasRole('technician')) {
                return redirect('/technician/dashboard');
            } elseif (auth()->user()->hasRole('user')) {
                return redirect('/dashboard');
            }
            
            // No recognized role
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
