<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RestrictLoginDuringMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        
        if ($request->is('login') && $request->isMethod('post')) { 

            $startTime = env('MAINTENANCE_START', '11:30');     //Default Start Time is Set to 03:00 AM
            $endTime   = env('MAINTENANCE_END', '13:30');       //Default End Time is Set to 05:00 PM


            // Allowed email(s)
            $allowedEmails = array_map('trim', explode(',', env('MAINTENANCE_ALLOWED_EMAILS', 'ak002@seqfast.com,ankushsingh5510@gmail.com')));

            // Current time
            $now = Carbon::now()->format('H:i');

            // Check if current time is in restricted window
            if ($this->isInMaintenanceWindow($startTime, $endTime, $now)) {

                // Check if user email is NOT in allowed list
                if (!in_array($request->input('email'), $allowedEmails)) {
                    return redirect()->back()
                        ->withErrors(['email' => 'Login is temporarily disabled due to system maintenance. Please try again later.']);
                }
            }
        }         
        return $next($request);
    }

    private function isInMaintenanceWindow($start, $end, $current)
    {
        // Handles normal and overnight ranges (e.g. 23:00 - 02:00)
        if ($start < $end) {
            return ($current >= $start && $current <= $end);
        } else {
            return ($current >= $start || $current <= $end);
        }
    }
}
