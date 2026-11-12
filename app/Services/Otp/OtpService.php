<?php
namespace App\Services\Otp;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Throwable;

class OtpService
{
    protected array $channels;

    public function __construct(
        EmailOtpChannel $emailChannel,
        Msg91OtpChannel $smsChannel
    ) {
        $this->channels = [
            'email' => $emailChannel,
            'sms'   => $smsChannel,
        ];
    }

    public function sendOtp(User $user, string $channel): void
    {
        if (!isset($this->channels[$channel])) {
            throw new RuntimeException('Invalid OTP channel.');
        }

        $recipient = $channel === 'email'
            ? $user->email
            : $user->phone;

        if (empty($recipient)) {
            throw new RuntimeException(
                ucfirst($channel) . ' recipient is not available.'
            );
        }

        // Limit OTP requests for this account and channel.
        $accountKey = 'otp-' . $user->id . '-' . $channel;

        if (RateLimiter::tooManyAttempts($accountKey, 1)) {
            $seconds = RateLimiter::availableIn($accountKey);

            throw new RuntimeException(
                "Please wait {$seconds} seconds before requesting another OTP."
            );
        }

        // Limit OTP requests from one IP across different accounts/channels.
        $ip = request()->ip() ?: 'unknown';
        $ipKey = 'otp-ip-' . hash('sha256', $ip);

        $ipMaxAttempts = 10;
        $ipDecaySeconds = 600; // 10 minutes

        if (RateLimiter::tooManyAttempts($ipKey, $ipMaxAttempts)) {
            $seconds = RateLimiter::availableIn($ipKey);

            throw new RuntimeException(
                "Too many OTP requests. Please try again in {$seconds} seconds."
            );
        }

        // Consume both limits before generating/sending the OTP.
        // Failed delivery also consumes the limits to prevent repeated abuse.
        RateLimiter::hit($accountKey, 120);
        RateLimiter::hit($ipKey, $ipDecaySeconds);

        $otp = (string) random_int(100000, 999999);

        $otpRecord = Otp::create([
            'user_id'    => $user->id,
            'channel'    => $channel,
            'recipient'  => $recipient,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'ip_address' => $ip,
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        try {
            $sent = $this->channels[$channel]->send($user, $otp);

            // A channel returning false means delivery failed.
            if ($sent === false) {
                throw new RuntimeException('OTP delivery failed.');
            }
        } catch (Throwable $e) {
            // Do not leave a usable OTP when delivery fails.
            $otpRecord->update([
                'expires_at' => now(),
            ]);

            throw $e;
        }

        // Expire previous pending OTPs only after the new delivery succeeds.
        Otp::where('user_id', $user->id)
            ->where('id', '<>', $otpRecord->id)
            ->whereNull('verified_at')
            ->update([
                'expires_at' => now(),
            ]);
    }
}
