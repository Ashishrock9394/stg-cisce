<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sstudent;

class AuthenticateStudent
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!Auth::guard('sstudent')->check()) {

            $rememberToken = $request->cookie('student_remember_token');

            if ($rememberToken) {
                $student = Sstudent::where('remember_token', $rememberToken)->first();
                if ($student) {
                    Auth::guard('sstudent')->login($student);
                    $request->session()->regenerate();
                    return $next($request);
                }
            }
           
            return redirect()->route('login');
        }

        return $next($request);
    }
}