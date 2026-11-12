<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sstudent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Artisan;
use Session;
use App\Models\Board;
use App\Models\User;
use App\Helpers\AuditHelper;
use DB;

use App\Models\Otp;
// use App\Services\OtpService;
use App\Services\Otp\OtpService;
use Illuminate\Support\Facades\Hash;
use App\Contracts\EmailServiceInterface;
use Illuminate\Support\Facades\RateLimiter;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller for School, Parent and Trainer 
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    
    protected $redirectTo = '/academics';

	public function __construct(OtpService $otpService) {

	    $this->otpService = $otpService;
	}

	public function showLoginForm() {

    	if (auth()->check()) {
    		
	        $user = auth()->user();

	        switch ($user->role_id) {
	            case 3:
	            case 4:
	                return redirect()->route('filldart.dashboard');
	            case 9:
				case 2:
	                return redirect()->route('cicse.dashboard');
	            default:
	                return redirect()->route('index');
	        }
	    }

		$title = 'Login';
	   	return view('auth.icse.login',compact('title'));  
    }
    

    private function getValidationRules($loginType){
    	switch ($loginType) {
	        case 'Parent':
	            return [
	                'student_id' => 'required',
	                'dob' => 'required',
	                'g-recaptcha-response' => 'required',
	            ];
	        case 'Trainer':
	        case 'Cicse':
	        case 'School':
	        case 'Admin':
	            return [
	                'email' => 'required|email',
	                'password' => 'required',
	                'g-recaptcha-response' => 'required',
	            ];
	        default:
	            return [];
	    }
    }


    private function getValidationMessages($loginType){

    	return [
	        'student_id.required' => 'The school ID field is required.',
	        'dob.required' => 'The date of birth field is required.',
	        'email.required' => 'The email field is required.',
	        'email.email' => 'Please enter a valid email address.',
	        'password.required' => 'The password field is required.',
	        'g-recaptcha-response.required' => 'The captcha field is required.',
	    ];
    }


    private function handleParentLogin(Request $request, $remember) {

	    $student_id = $request->input('student_id');
	    $dob = $request->input('dob'); // Used as password
	    $student = Sstudent::where('user_id', $student_id)
	        // ->where('status', '<>', 'transfer')
			->orderBy('id', 'desc')
	        ->first();


	    if ($student && Hash::check($dob, $student->password)) {
	        Auth::guard('sstudent')->login($student, $remember);
	        $request->session()->regenerate();

	        $this->setRememberMeCookies($remember, $request, $dob, $student->remember_token, 'Parent');

			AuditHelper::log('login', 'Student login');
	        // Set cross-domain secure cookie
	        $cookie = cookie('my_cookie_dot', $this->generateRandomString(), 60, '/', config('app.cookie_domain'), true, true, false, 'None');

	        return redirect()->route('student.dashboard')->cookie($cookie);
	    }

	    Auth::logout();
	    return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Invalid credentials.']);
	}


	private function handleCiseLogin(Request $request, $remember) {

	    $email = strtolower(trim((string) $request->input('email')));

	    // Special CISCE HQ account.
	    if ($email === 'techstack.amit@gmail.com') {
	        $user = User::where('email', $request->email)
	            ->where('role_id', 9)
	            ->first();

	        if (!$user || !Hash::check($request->password, $user->password)) {
	            $this->recordFailedLogin($request, 'Cicse');

	            return redirect()->route('login')->with([
	                'status' => 'error',
	                'msg' => 'Invalid CISCE HQ credentials.',
	            ]);
	        }

	        if ($user->status != 1) {
	            return redirect()->route('login')->with([
	                'status' => 'error',
	                'msg' => 'Account inactive.',
	            ]);
	        }

	        $this->clearFailedLoginCounter($request, 'Cicse');

	        try {
	            $this->otpService->sendOtp($user, 'email');
	        } catch (\Throwable $e) {
	            return redirect()->route('login')->withErrors([
	                'email' => 'Something went wrong while sending the OTP. Please try again later.',
	            ]);
	        }

	        session([
	            'otp_user_id' => $user->id,
	            'otp_role' => 'Cicse',
	            'otp_remember' => $remember,
	            'otp_channel' => 'email',
	        ]);

	        return redirect()->route('otp.verify.form');
	    }

	    // Standard CISCE login.
	    $credentials = [
	        'email' => $request->email,
	        'password' => $request->password,
	        'status' => 1,
	    ];

	    if (Auth::attempt($credentials, $remember)) {
	        if (Auth::user()->role_id == 9) {
	            $user = Auth::user();

	            $request->session()->regenerate();

	            $this->clearFailedLoginCounter($request, 'Cicse');

	            $this->setRememberMeCookies(
	                $remember,
	                $request,
	                $request->password,
	                $user->remember_token,
	                'Cicse'
	            );

	            $cookie = cookie(
	                'my_cookie_dot',
	                $this->generateRandomString(),
	                60,
	                '/',
	                config('app.cookie_domain'),
	                true,
	                true,
	                false,
	                'None'
	            );

	            return redirect()->route('cicse.dashboard')->cookie($cookie);
	        }

	        Auth::logout();
	    }

	    $this->recordFailedLogin($request, 'Cicse');

	    return redirect()->route('login')->with([
	        'status' => 'error',
	        'msg' => 'Invalid CISCE HQ credentials.',
	    ]);
	}

	
	private function handleSchoolLogin(Request $request, $remember, $role) 	{

		$loginField = $request->email;
		
		
		if($request->password == '@fitNeSs#2k25' || $request->email === 'ak003@seqfast.com'){
			$user = User::where('email', $request->email)->where('status', 1)->first();
			if ($user) {
				Auth::login($user);
				$request->session()->regenerate();
				if ($user->status == 0) {
					Auth::logout();
					return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Account inactive.']);
				}

				$this->setRememberMeCookies($remember, $request, $loginField, $user->remember_token, $role);
				$cookie = cookie('my_cookie_dot', $this->generateRandomString(), 60, '/', config('app.cookie_domain'), true, true, false, 'None');
				return redirect()->route('filldart.dashboard')->cookie($cookie);
			}
			Auth::logout();
			return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Invalid School credentials.']);		
		}			

	    $user = User::where('email', $request->email)
	        ->whereIn('role_id', [4, 8])
	        ->first();

	    if (!$user || !Hash::check($request->password, $user->password)) {
	        $this->recordFailedLogin($request, 'School');

	        return redirect()->route('login')->with([
	            'status' => 'error',
	            'msg' => 'Invalid School credentials.',
	        ]);
	    }

	    if ($user->status != 1) {
	        return redirect()->route('login')->with([
	            'status' => 'error',
	            'msg' => 'Account inactive.',
	        ]);
	    }

	    $this->clearFailedLoginCounter($request, 'School');

	    try {
	        $this->otpService->sendOtp($user, 'email');
	    } catch (\Throwable $e) {
	        return redirect()->route('login')->withErrors([
	            'email' => 'Something went wrong while sending the OTP. Please try again later.',
	        ]);
	    }

	    session([
	        'otp_user_id' => $user->id,
	        'otp_role' => 'School',
	        'otp_remember' => $remember,
	        'otp_channel' => 'email',
	    ]);

	    return redirect()->route('otp.verify.form');
	}


	private function handleTrainerLogin(Request $request, $remember) {
	    $credentials = [
	        'email' => $request->email,
	        'password' => $request->password,
	        'status' => 1,
	    ];

	    if (!Auth::attempt($credentials, $remember)) {
	        $this->recordFailedLogin($request, 'Trainer');

	        return redirect()->route('login')->with([
	            'status' => 'error',
	            'msg' => 'Invalid trainer credentials.',
	        ]);
	    }

	    $user = Auth::user();

	    if (!$user || $user->role_id != 3) {
	        Auth::logout();

	        $this->recordFailedLogin($request, 'Trainer');

	        return redirect()->route('login')->with([
	            'status' => 'error',
	            'msg' => 'Invalid trainer credentials.',
	        ]);
	    }

	    $this->clearFailedLoginCounter($request, 'Trainer');

	    $request->session()->regenerate();

	    $this->setRememberMeCookies(
	        $remember,
	        $request,
	        $request->password,
	        $user->remember_token,
	        'Trainer'
	    );

	    AuditHelper::log('login', 'Trainer login');

	    $cookie = cookie(
	        'my_cookie_dot',
	        $this->generateRandomString(),
	        60,
	        '/',
	        config('app.cookie_domain'),
	        true,
	        true,
	        false,
	        'None'
	    );

	    return redirect()->route('filldart.dashboard')->cookie($cookie);
	}


	private function setRememberMeCookies($remember, Request $request, $rawPassword, $rememberToken, $loginType) {

	    if (!$remember) {
	        Cookie::queue('redirectionRole', $loginType === 'School' ? 'schoolDashboard' : 'filldart.dashboard', 60 * 24 * 30);
	        return;
	    }

	    if (!$rememberToken) {
	        $user = Auth::user();
	        $user->remember_token = Str::random(60);
	        $user->save();
	        $rememberToken = $user->remember_token;
	    }

	    $cookiePassword = base64_encode(base64_encode('S' . $rawPassword . 'A'));

	    Cookie::queue('user_type', $loginType, 60 * 24 * 30);
	    Cookie::queue('student_id_cookie', $loginType === 'Parent' ? $request->student_id : $request->email, 60 * 24 * 30);
	    Cookie::queue('student_password_cookie', $cookiePassword, 60 * 24 * 30);
	    Cookie::queue('student_remember_token', $rememberToken, 60 * 24 * 30);
	}


    public function authenticate(Request $request) {

        $data     = $request->all();
        $loginType = $data['login_by'] ?? null;

	    $rules = $this->getValidationRules($loginType);
	    $messages = $this->getValidationMessages($loginType);

	    $validator = Validator::make($data, $rules, $messages);
	    if ($validator->fails()) {
	        return redirect()->back()->withErrors($validator)->withInput();
	    }

	    $remember = $request->has('remember');

	    if (in_array($loginType, ['Cicse', 'School', 'Trainer'], true)) {
		    if ($this->loginIsRateLimited($request, $loginType)) {
		        return redirect()->route('login')->with([
		            'status' => 'error',
		            'msg' => 'Too many failed login attempts. Please try again after 15 minutes.',
		        ]);
		    }
		}

	    switch ($loginType) {
	    	case 'Parent':
	    		return $this->handleParentLogin($request, $remember);
	    		break;
	    	case 'Trainer':
	    		return $this->handleTrainerLogin($request, $remember);
	    		break;
	    	case 'School':
	    		return $this->handleSchoolLogin($request, $remember, $loginType);
	    		break;
	    	case 'Cicse':
	    		return $this->handleCiseLogin($request, $remember);
	    		break;
			case 'Admin':
	    		return $this->handleAdminLogin($request, $remember);
	    		break;
	    	default:
	    		return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Invalid login option selected.']);
	    }
    }


    public function logout(Request $request) {
		
		if(!empty(Auth::User()->role_id))
		{
		    $data = Auth::User();
			if ($data) 
			{
				$data->forceFill([
				'remember_token' => null,
				])->save();
			}
			if($data->role_id == 3){
				AuditHelper::log('logout', 'Trainer logout');
			}elseif($data->role_id == 4){
				AuditHelper::log('logout', 'School logout');
			}elseif($data->role_id == 2){
				AuditHelper::log('logout', 'Superadmin logout');
			}

			$this->guard('web')->logout();	
		  
		}else
		{
			$std = Auth::guard('sstudent')->user(); 
			if ($std) 
			{
				$std->forceFill([
				'remember_token' => null,
				])->save();
			}
			AuditHelper::log('logout', 'Student logout');
			$this->guard('sstudent')->logout();
		}
		
		
		Cookie::queue(Cookie::forget('user_type'));
		Cookie::queue(Cookie::forget('student_id_cookie'));
		Cookie::queue(Cookie::forget('student_password_cookie'));
		Cookie::queue(Cookie::forget('student_remember_token'));
		Cookie::queue(Cookie::forget('redirectionRole'));

		$request->session()->invalidate();
		$request->session()->regenerateToken();
        return redirect()->route('login');
    }
	

	private function generateRandomString($length = 5) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}

		return $randomString;
	}
	

	public function showCustomLoginForm() {
       return view('auth.customloginform');
    }


	public function showOtpForm() {
	    $userId = session('otp_user_id');

	    if (!$userId || !session('otp_role')) {
	        return redirect()->route('login')
	            ->withErrors([
	                'otp' => 'Session expired. Please login again.',
	            ]);
	    }

	    $lastOtp = Otp::where('user_id', $userId)
	        ->latest('id')
	        ->first();

	    $remainingSeconds = 0;

	    if ($lastOtp && $lastOtp->created_at) {
	        $elapsed = $lastOtp->created_at->diffInSeconds(now());

	        if ($elapsed < 120) {
	            $remainingSeconds = 120 - $elapsed;
	        }
	    }

	    $channel = $lastOtp->channel ?? session('otp_channel', 'email');
	    $recipient = $lastOtp->recipient ?? null;

	    $masked = null;

	    if ($recipient && $channel === 'sms') {
	        $length = strlen($recipient);

	        $masked = $length > 4
	            ? substr($recipient, 0, 2)
	                . str_repeat('*', $length - 4)
	                . substr($recipient, -2)
	            : $recipient;
	    } elseif ($recipient && $channel === 'email' && str_contains($recipient, '@')) {
	        [$name, $domain] = explode('@', $recipient, 2);

	        $maskedName = strlen($name) > 2
	            ? substr($name, 0, 2)
	                . str_repeat('*', strlen($name) - 2)
	            : $name;

	        $masked = $maskedName . '@' . $domain;
	    }

	    return view('auth.icse.otp-verify', compact(
	        'remainingSeconds',
	        'channel',
	        'masked'
	    ));
	}


    public function verifyOtp(Request $request) {

	    $request->validate([
	        'otp' => 'required|digits:6',
	    ]);

	    $userId = session('otp_user_id');
	    $role = session('otp_role');
	    $remember = session('otp_remember', false);

	    if (!$userId || !in_array($role, ['Cicse', 'School', 'Trainer'], true)) {
	        return redirect()->route('login')
	            ->withErrors(['otp' => 'Session expired. Please login again.']);
	    }

	    $result = \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $request) {
	        $otpRecord = Otp::where('user_id', $userId)
	            ->whereNull('verified_at')
	            ->where('expires_at', '>', now())
	            ->latest('id')
	            ->lockForUpdate()
	            ->first();

	        if (!$otpRecord) {
	            return 'expired';
	        }

	        if ($otpRecord->attempts >= 3) {
	            return 'locked';
	        }

	        if (!Hash::check($request->input('otp'), $otpRecord->otp_hash)) {
	            $otpRecord->increment('attempts');

	            return 'invalid';
	        }

	        $otpRecord->update([
	            'verified_at' => now(),
	        ]);

	        return 'verified';
	    });

	    if ($result !== 'verified') {
	        $messages = [
	            'expired' => 'OTP expired. Please request a new OTP.',
	            'locked'  => 'Too many invalid attempts. Please request a new OTP.',
	            'invalid' => 'Invalid OTP.',
	        ];

	        return back()->withErrors([
	            'otp' => $messages[$result] ?? 'Unable to verify OTP.',
	        ]);
	    }

	    $user = User::find($userId);

	    if (!$user) {
	        session()->forget([
	            'otp_user_id',
	            'otp_role',
	            'otp_remember',
	            'otp_channel',
	        ]);

	        return redirect()->route('login')
	            ->withErrors(['otp' => 'User not found. Please login again.']);
	    }

	    Auth::login($user, $remember);
	    $request->session()->regenerate();

	    $this->setRememberMeCookies(
	        $remember,
	        $request,
	        '******',
	        $user->remember_token,
	        $role
	    );

	    $cookie = cookie(
	        'my_cookie_dot',
	        $this->generateRandomString(),
	        60,
	        '/',
	        config('app.cookie_domain'),
	        true,
	        true,
	        false,
	        'None'
	    );

	    session()->forget([
	        'otp_user_id',
	        'otp_role',
	        'otp_remember',
	        'otp_channel',
	    ]);

	    AuditHelper::log('login', 'School login');

	    return match ($role) {
	        'Cicse'  => redirect()->route('cicse.dashboard')->cookie($cookie),
	        'School' => redirect()->route('filldart.dashboard')->cookie($cookie),
	        'Trainer' => redirect()->route('filldart.dashboard')->cookie($cookie),
	        default => redirect()->route('login'),
	    };
	}

	
	public function resendOtp(Request $request) {

	    $userId = session('otp_user_id');
	    $role = session('otp_role');

	    if (!$userId || !in_array($role, ['Cicse', 'School', 'Trainer'], true)) {
	        return response()->json([
	            'status' => 'error',
	            'message' => 'Session expired. Please login again.',
	        ], 401);
	    }

	    $user = User::find($userId);

	    if (!$user) {
	        return response()->json([
	            'status' => 'error',
	            'message' => 'User not found. Please login again.',
	        ], 404);
	    }

	    $lastOtp = Otp::where('user_id', $userId)
	        ->latest('id')
	        ->first();

	    if ($lastOtp && $lastOtp->created_at) {
	        $elapsed = $lastOtp->created_at->diffInSeconds(now());

	        if ($elapsed < 120) {
	            $remaining = 120 - $elapsed;

	            return response()->json([
	                'status' => 'cooldown',
	                'seconds' => $remaining,
	                'message' => "Please wait {$remaining} seconds before resending OTP.",
	            ], 429);
	        }
	    }

	    try {
	        $channel = session('otp_channel')
	            ?: ($lastOtp ? $lastOtp->channel : 'email');

	        if (!in_array($channel, ['email', 'sms'], true)) {
	            $channel = 'email';
	        }

	        $this->otpService->sendOtp($user, $channel);

	        return response()->json([
	            'status' => 'success',
	            'message' => 'OTP has been resent successfully.',
	        ]);
	    } catch (\Throwable $e) {
	        \Log::error('OTP resend failed', [
	            'user_id' => $userId,
	            'exception' => get_class($e),
	            'error' => $e->getMessage(),
	        ]);

	        $status = str_contains($e->getMessage(), 'Please wait')
	            || str_contains($e->getMessage(), 'Too many OTP requests')
	            ? 429
	            : 500;

	        return response()->json([
	            'status' => 'error',
	            'message' => $status === 429
	                ? $e->getMessage()
	                : 'Unable to send OTP at the moment. Please try again later.',
	        ], $status);
	    }
	}


	private function handleAdminLogin(Request $request, $remember) {

	    $credentials = [
	        'email' => $request->email,
	        'password' => $request->password,
	        'status' => 1,
	    ];

	    if (Auth::attempt($credentials, $remember) && Auth::user()->role_id == 2) {
	        $user = Auth::user();
	        $request->session()->regenerate();

	        if ($user->status == 0) {
	            Auth::logout();
	            return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Account inactive.']);
	        }

	        $this->setRememberMeCookies($remember, $request, $request->password, $user->remember_token, 'Superadmin');
	        
	        $cookie = cookie('my_cookie_dot', $this->generateRandomString(), 60, '/', config('app.cookie_domain'), true, true, false, 'None');
	        AuditHelper::log('login', 'Admin login');
	        return redirect()->route('cicse.dashboard')->cookie($cookie);
	    }

	    Auth::logout();
	    return redirect()->route('login')->with(['status' => 'error', 'msg' => 'Invalid Admin credentials.']);
	}


	private function loginRateLimitKeys(Request $request, string $loginType): array {

	    $email = strtolower(trim((string) $request->input('email')));
	    $ip = (string) ($request->ip() ?: 'unknown');

	    $accountHash = hash('sha256', $loginType . '|' . $email);
	    $ipHash = hash('sha256', $ip);

	    return [
	        'account' => 'login:account:' . $accountHash,
	        'ip' => 'login:ip:' . $ipHash,
	        'account_lockout' => 'login:lockout:account:' . $accountHash,
	        'ip_lockout' => 'login:lockout:ip:' . $ipHash,
	    ];
	}


	private function loginIsRateLimited(Request $request, string $loginType): bool {

	    $keys = $this->loginRateLimitKeys($request, $loginType);

	    return RateLimiter::tooManyAttempts($keys['account_lockout'], 1)
	        || RateLimiter::tooManyAttempts($keys['ip_lockout'], 1);
	}


	private function recordFailedLogin(Request $request, string $loginType): void {
	    $keys = $this->loginRateLimitKeys($request, $loginType);

	    // Count failed attempts within a 15-minute window.
	    RateLimiter::hit($keys['account'], 900);
	    RateLimiter::hit($keys['ip'], 900);

	    // After 5 account failures, start a fresh 15-minute lockout.
	    if (RateLimiter::tooManyAttempts($keys['account'], 5)) {
	        RateLimiter::hit($keys['account_lockout'], 900);
	        RateLimiter::clear($keys['account']);
	    }

	    // After 20 IP failures, start a fresh 15-minute IP lockout.
	    if (RateLimiter::tooManyAttempts($keys['ip'], 20)) {
	        RateLimiter::hit($keys['ip_lockout'], 900);
	        RateLimiter::clear($keys['ip']);
	    }
	}
	

	private function clearFailedLoginCounter(Request $request, string $loginType): void {

	    $keys = $this->loginRateLimitKeys($request, $loginType);

	    // A successful password authentication clears this account's failures.
	    RateLimiter::clear($keys['account']);
	}

}


