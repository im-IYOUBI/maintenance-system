<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckRole
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
            throw UnauthorizedException::notLoggedIn();
        }

        $roles = is_array($role) ? $role : explode('|', $role);
        
        if (!auth()->user()->hasAnyRole($roles)) {
            // Store intended URL for proper redirection after login
            if (auth()->check()) {
                // User is logged in but doesn't have the right role
                if (auth()->user()->hasRole('admin')) {
                    return redirect('/admin/dashboard');
                } elseif (auth()->user()->hasRole('technician')) {
                    return redirect('/technician/dashboard');
                } elseif (auth()->user()->hasRole('user')) {
                    return redirect('/dashboard');
                }
                
                // User has no recognized role
                return redirect('/')->with('error', 'You do not have the required permissions.');
            }
            
            // User is not logged in
            return redirect()->route('login');
        }

        return $next($request);
    }
}
