<?php 

namespace App\Services\Otp;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use App\Services\Otp\EmailOtpChannel;
use App\Services\Otp\Msg91OtpChannel;

class OtpService
{
    protected array $channels;

    public function __construct( EmailOtpChannel $emailChannel, Msg91OtpChannel $smsChannel ) {

        $this->channels = [
            'email' => $emailChannel,
            'sms'   => $smsChannel,
        ];
    }

    public function sendOtp(User $user, string $channel): void {

        if (!isset($this->channels[$channel])) {
            throw new \Exception('Invalid OTP channel');
        }

        $recipient = match ($channel) {
	        'email' => $user->email,
	        'sms'   => $user->phone
	    };

	    if (empty($recipient)) {
	        throw new \Exception(
	            ucfirst($channel) . ' recipient not available for OTP'
	        );
	    }

		// Rate limiter for otp 
		$key = 'otp-'.$user->id.'-'.$channel;

		if (RateLimiter::tooManyAttempts($key, 1)) {

			$seconds = RateLimiter::availableIn($key);

			throw new \Exception(
				"Please wait {$seconds} seconds before requesting another OTP."
			);
		}

		// Allow 1 OTP every 60 seconds
		RateLimiter::hit($key, 120);


        $otp = random_int(100000, 999999);
        Otp::where('user_id', $user->id)->whereNull('verified_at')->update(['expires_at' => now()]);
        
    	Otp::create([
	        'user_id'    => $user->id,
	        'channel'    => $channel,
	        'recipient'  => $recipient,
	        'otp_hash'   => Hash::make($otp),
	        'expires_at' => now()->addMinutes(5),
	        'ip_address' => request()->ip(),
	        'user_agent' => substr(request()->userAgent(), 0, 255),
	    ]);
        
        $this->channels[$channel]->send($user, (string) $otp);
    }
}


