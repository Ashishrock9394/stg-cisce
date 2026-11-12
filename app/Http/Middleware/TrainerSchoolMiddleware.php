<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;

class TrainerSchoolMiddleware 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next, ...$guards)
    {
		
		   if(!Auth::check()) 
		   {
				$rememberToken = $request->cookie('student_remember_token');

				if ($rememberToken) 
				{
					// Use the User model directly
					$student = User::where('remember_token', $rememberToken)->first();
					if ($student) 
					{
						Auth::login($student);
						$request->session()->regenerate();
						return $next($request);
					}
				
				}

				return redirect()->route('login.test');
			}

			return $next($request);
    }

   /* protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login.test');
        }
    }*/
}
