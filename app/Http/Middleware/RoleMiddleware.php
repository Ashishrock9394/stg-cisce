<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Allow impersonation sessions
        if (session()->has('Auth_id') && session()->has('is_impersonating')) {
            return $next($request);
        }
        
        if(auth()->check() && in_array(auth()->user()->role_id, array_map('intval', $roles))){
            return $next($request);
        }
        return redirect('login')->with('error', 'Unauthorized access.');
    }
}
