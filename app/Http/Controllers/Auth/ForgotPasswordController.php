<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\Otp\EmailApiService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct (EmailApiService $emailApiService){

        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role_id === 4) {
                return redirect()->route('filldart.dashboard');
            }

            return $next($request);
        });
        $this->emailApiService = $emailApiService;
    }

    /**
     * Custom functions to handle Password reset functionlity using backup Recovery Code.
     * Verify security code for password recovery
     */
    public function verifySecurityCode(Request $request) {
        
        // \Log::info('Security code verification request', $request->all());

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'security_code' => 'required|string'
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'No account found with this email address',
            'security_code.required' => 'Security code is required'
        ]);

        if ($validator->fails()) {
            \Log::error('Security code validation failed', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if user has a security code set
            if (!$user->security_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'No security code found for this account'
                ], 422);
            }

            // Verify security code
            if (Hash::check($request->security_code, $user->security_code)) {
               
                if ($user->security_code_generated_at) {
                    $expiryDate = $user->security_code_generated_at->addDays(30);
                    if (now()->gt($expiryDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Security code has expired. Please use another recovery method.'
                        ], 422);
                    }
                }

                // Store verification in session for password reset
                session([
                    'recovery_user_id' => $user->id,
                    'recovery_verified' => true,
                    'recovery_method' => 'backup_code'
                ]);

                \Log::info('Security code verified successfully for user: ' . $user->id);

                return response()->json([
                    'success' => true,
                    'message' => 'Security code verified successfully'
                ]);
            }

            \Log::warning('Invalid security code attempt for user: ' . $user->id);

            return response()->json([
                'success' => false,
                'message' => 'Invalid security code'
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Security code verification error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify security code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendResetOtp(Request $request){

        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found.'
            ], 404);
        }
        session([
            'otp_user_id'  => $user->id,
            'otp_role' => $user->role_id == 4 ? 'School' : ($user->role_id == 3 ? 'Trainer' : null),
            'otp_channel'  => 'email'
        ]);

        $otp = random_int(100000, 999999);
        Otp::where('user_id', $user->id)->whereNull('verified_at')->update(['expires_at' => now()]);
        
    	Otp::create([
	        'user_id'    => $user->id,
	        'channel'    => 'email',
	        'recipient'  => $request->email,
	        'otp_hash'   => Hash::make($otp),
	        'expires_at' => now()->addMinutes(5),
	        'ip_address' => request()->ip(),
	        'user_agent' => substr(request()->userAgent(), 0, 255),
	    ]);

        $this->emailApiService->sendPasswordResetOtp($user, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.'
        ]);
    }

    public function verifyResetOtp(Request $request) {

	    $request->validate(['otp' => 'required|digits:6']);

	    $userId = session('otp_user_id');
	    $role   = session('otp_role');
	    $remember = session('otp_remember');

	    if (!$userId || !$role) {
	        return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please login again.']);
	    }
        $user = User::where('id', $userId)->first();

	    $otpRecord = Otp::where('user_id', $userId)->whereNull('verified_at')->where('expires_at', '>', now())->latest()->first();

	    $html = route('login');
		if (!$otpRecord) {
	        return back()->withErrors(['otp' => 'OTP expired.','route' => $html]);
	    }


	    if ($otpRecord->attempts >= 3) {
	        return back()->withErrors(['otp' => 'Too many invalid attempts.','route' => $html]);
	    }

	    if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
	        $otpRecord->increment('attempts');
	        return back()->withErrors(['otp' => 'Invalid OTP']);
	    }

	    $otpRecord->update([
	        'verified_at' => now()
	    ]);


        $token = Password::broker()->createToken($user);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect_url' => route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]),
        ]);
	
	}
}
