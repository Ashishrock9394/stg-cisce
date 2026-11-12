<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckProfileUpdate
{

    public function handle(Request $request, Closure $next) {

        $user = Auth::guard('sstudent')->user();
        if ($user && $user->last_updated) {

            $lastUpdated = Carbon::parse($user->last_updated);
            if ($lastUpdated->diffInDays(Carbon::now()) > 90) {       //check for 90 days (three months) from the current date
                if (!session()->has('updateprofile')) {
                    session()->flash('updateprofile', true);
                }
            }
        }

        return $next($request);
    }
}
