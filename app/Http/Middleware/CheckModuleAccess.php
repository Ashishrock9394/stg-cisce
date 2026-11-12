<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $moduleSlug
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $moduleSlug)
    {
        $user = Auth::user();

        if ($user->role_id == 4) {
            return $next($request);
        }

        $allowedModules = json_decode($user->usermeta->module_access ?? '[]', true); 
        // echo"<pre>";print_r($allowedModules);exit();  


        if (!in_array($moduleSlug, $allowedModules)) {
            abort(404, "You don't have access to this module");
        }

        return $next($request);
    }
}
