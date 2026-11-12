<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Contracts\OtpChannelInterface;
use Illuminate\Support\Facades\Mail;

class Msg91OtpChannel implements OtpChannelInterface {


    public function send(User $user, string $otp): bool {

        if (empty($user->phone)) {
            Log::warning('OTP SMS skipped: Mobile not found', ['user_id' => $user->id]);
            return false;
        }

        $mobile = preg_replace('/\D/', '', $user->phone);
        if (strlen($mobile) !== 10) {
            Log::error('Invalid mobile number', ['mobile' => $user->phone]);
            return false;
        }

        $message = "{$otp} is the Onetime password (OTP) for Active CISCE login authentication. Please do not share OTP for security reasons. CISCE";

        try {

            $response = Http::timeout(10)->get('https://api.msg91.com/api/sendhttp.php', [
                'authkey'   => config('services.msg91.authkey'),
                'sender'    => config('services.msg91.sender'), 
                'mobiles'   => '91' . $user->phone,
                'route'     => 4,
                'message'   => $message,
                'DLT_TE_ID' => config('services.msg91.dlt_te_id'),
            ]);

            return $response->successful();

            if (! $response->successful() || str_contains(strtoupper($response->body()), 'ERROR')) {
                Log::error('MSG91 SMS failed', [
                    'user_id' => $user->id,
                    'mobile'  => $user->phone,
                    'response'=> $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {

            Log::critical('MSG91 SMS exception', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            throw new Exception("MSG91 SMS exception");
        }
    }
}
