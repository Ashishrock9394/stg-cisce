<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class TimeBasedRouteAccess
{
    public function handle(Request $request, Closure $next)
    {
        $startDate = Carbon::parse('2026-05-01 00:00:00');
        $endDate   = Carbon::parse('2026-12-31 17:00:00');

        $now = Carbon::now();

        $restrictedRoutes = [
            // 'all-test' => [
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'The Assessment feature will be available after 31st July. Please ensure that all students are promoted through the “Manage Students” section under the School Login panel. Once the promotion process is completed, all schools will receive the official circular regarding the same.',    
            // ],

            // 'fms-types' => [
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'The Assessment feature will be available after 31st July. Please ensure that all students are promoted through the “Manage Students” section under the School Login panel. Once the promotion process is completed, all schools will receive the official circular regarding the same..',    
            // ],

            // 'assessor-app-test' => [
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'The Assessment feature will be available after 31st July. Please ensure that all students are promoted through the “Manage Students” section under the School Login panel. Once the promotion process is completed, all schools will receive the official circular regarding the same..',    
            // ],

            // 'assessor.app.physical.test' => [
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'The Assessment feature will be available after 31st July. Please ensure that all students are promoted through the “Manage Students” section under the School Login panel. Once the promotion process is completed, all schools will receive the official circular regarding the same..',    
            // ],

            // 'assessor.app.physical.senior.test' => [
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'The Assessment feature will be available after 31st July. Please ensure that all students are promoted through the “Manage Students” section under the School Login panel. Once the promotion process is completed, all schools will receive the official circular regarding the same..',    
            // ],

            'upload.test.data' => [ 
                'title' => 'Excel Import Restricted on Principal Interface',
                'message' => '<div style="background-color: #fff9db; border-left: 6px solid #f59f00; padding: 16px; margin: 20px 0; border-radius: 4px; font-family: sans-serif; color: #2b2b2b;">
                    
                    <p style="margin: 0 0 12px 0; font-size: 14px; line-height: 1.5;">
                        All schools are instructed to conduct fitness assessments using the <strong>Take Test</strong> module available in the Trainer Portal (PE Teachers / Coaches).
                    </p>
                    <p style="margin: 0 0 12px 0; font-size: 14px; line-height: 1.5;">
                   Direct Excel import of offline fitness assessment is restricted through the Principal login to prevent data conflicts during assessments by PE Teachers/Coaches.
                    </p>
                </div>'
            ],

            // 'higherclasstestsummary' => [ 
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'No students are available in current session. Please promote students from last session/ upload new student from "Manage Students" under School Login.'
            // ],
            // 'lowerclasstestsummary' => [ 
            //     'title' => 'Assessments Unavailable',
            //     'message' => 'No students are available in current session. Please promote students from last session/ upload new student from "Manage Students" under School Login.'
            // ],

            // 'managestudent' => [
            //     'title' => 'Temporarily disabled',
            //     'message' => 'The "Manage Students" module is temporarily unavailable as we prepare for Student Promotions. This feature will return once the promotion training process under Active CISCE guidelines is complete.'
            // ],
        ];

        $allowedUsers = [3139, 3251, 5365, 4065, 5551];

        $user = Auth::user();

        $routeName = $request->route()?->getName();
        if (array_key_exists($routeName, $restrictedRoutes)) {
            if ($user && in_array($user->id, $allowedUsers)) {
                return $next($request);
            }
            if ($now->between($startDate, $endDate)) {

                $routeData = $restrictedRoutes[$routeName];
                //$message = $restrictedRoutes[$routeName];
                return response()->view(
                    'errors.403',
                    [
                        'title'   => $routeData['title'],
                        'message' => $routeData['message'],
                        'routeName' => $routeName
                    ],
                    403
                );
            }
        }

        return $next($request);
    }
}