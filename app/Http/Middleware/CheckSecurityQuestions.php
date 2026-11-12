<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSecurityAnswer;


class CheckSecurityQuestions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $user = Auth::user();
        $count = UserSecurityAnswer::where('user_id', $user->id)->count();
        if ($count == 0) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please setup your security questions first',
                ], 403);
            }

            return redirect()->route('password.change')
                ->with('error', 'Please setup your security questions first.');
        }

        return $next($request);
    }
}
