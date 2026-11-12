<?php

namespace App\Services\Otp;

use App\Contracts\OtpChannelInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Otp\EmailApiService;
use Exception;

class EmailOtpChannel implements OtpChannelInterface {

    public function __construct(
        protected EmailApiService $emailApiService
    ) {}

    public function send(User $user, string $otp): bool
    {
            
        return $this->emailApiService->sendOtp($user, $otp);
    }

}
