<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckAdminRole
{
    /**
     * Handle an incoming request to ensure the user has admin role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            // Not logged in - redirect to login
            return redirect()->route('login');
        }

        if (!auth()->user()->hasRole('admin')) {
            // User doesn't have admin role - redirect based on their role
            if (auth()->user()->hasRole('technician')) {
                return redirect('/technician/dashboard');
            } elseif (auth()->user()->hasRole('user')) {
                return redirect('/dashboard');
            }
            
            // No recognized role
            throw UnauthorizedException::forRoles(['admin']);
        }

        return $next($request);
    }
}
