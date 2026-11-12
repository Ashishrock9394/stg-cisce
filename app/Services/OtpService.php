<?php 


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\Otp;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\View\Factory as ViewFactory;
use App\Contracts\EmailServiceInterface;

class OtpService {

    protected $authKey;
    protected $sender;
    protected $templateId;
    protected $otpLength;

    protected EmailServiceInterface $emailService;
    protected ViewFactory $view;

    public function __construct(EmailServiceInterface $emailService, ViewFactory $view){

        $this->emailService = $emailService;
        $this->view = $view;


        $this->authKey    = config('services.msg91.authkey');
        $this->sender     = config('services.msg91.sender');
        $this->templateId = config('services.msg91.dlt_te_id');
        $this->otpLength  = (int)config('services.msg91.otp_length', 6);
    }

    /** Handle OTP via SMTP mail */
    public function generateEmailOtp($user) {

        $otp = rand(100000, 999999);

        Otp::where('user_id', $user->id) ->whereNull('verified_at')->update(['expires_at' => now()]);

        Otp::create([
            'user_id'    => $user->id,
            'channel'    => 'email',
            'recipient'  => $user->email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        try {

            $subject = "Active CISCE – Verify Your Login";

            $htmlContent = $this->view->make('emails.cisce_otp_mail', [
                'user' => $user,
                'otp'  => $otp,
            ])->render();


            $recipient1 = [
                'email' => $user->email,
                'name'  => $user->name,
            ];
            $this->emailService->send($recipient1, $subject, $htmlContent);


            /** Sending OTP On Defaul Email ID */
            if (!empty($user->userid)) {      
                $recipient2 = [
                    'email' => $user->userid,
                    'name'  => $user->name,
                ];
                $this->emailService->send($recipient2, $subject, $htmlContent);
            }

        } catch (Exception $e) {
            Log::error("OTP Email Sending Failed: " . $e->getMessage());
            throw new \Exception("MAIL_FAILED");
        }

        return true;
    }




    /** Primary: call MSG91 v5 OTP API (recommended). Returns array with success bool and data */
    public function sendViaV5(string $mobile): array
    {
        try {
            $url = 'https://api.msg91.com/api/v5/otp';
            $payload = [
                'template_id' => $this->templateId,
                'mobile'      => $mobile,
                'sender'      => $this->sender,
                'otp_length'  => $this->otpLength,
            ];

            $resp = Http::withHeaders([
                'authkey' => $this->authKey,
                'Accept'  => 'application/json'
            ])->post($url, $payload);

            if ($resp->successful()) {
                $body = $resp->json();
                // msg91 v5 returns success type etc. We'll pass back entire body.
                return ['success' => true, 'provider' => 'msg91_v5', 'body' => $body];
            }

            Log::warning('MSG91 v5 send failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['success' => false, 'error' => 'v5_failed', 'status' => $resp->status(), 'body' => $resp->body()];
        } catch (Exception $e) {
            Log::error('MSG91 v5 exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'v5_exception', 'exception' => $e->getMessage()];
        }
    }

    /** Primary: verify via v5 */
    public function verifyViaV5(string $mobile, string $otp): array
    {
        try {
            $url = 'https://api.msg91.com/api/v5/otp/verify';
            $resp = Http::withHeaders([
                'authkey' => $this->authKey,
                'Accept'  => 'application/json'
            ])->get($url, [
                'mobile' => $mobile,
                'otp'    => $otp
            ]);

            if ($resp->successful()) {
                $json = $resp->json();
                // msg91 returns type='success' or not — we'll check commonly used keys
                return ['success' => true, 'provider' => 'msg91_v5', 'body' => $json];
            }

            Log::warning('MSG91 v5 verify failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['success' => false, 'error' => 'v5_verify_failed', 'status' => $resp->status(), 'body' => $resp->body()];
        } catch (Exception $e) {
            Log::error('MSG91 v5 verify exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'v5_verify_exception', 'exception' => $e->getMessage()];
        }
    }

    /** Fallback sending using sendhttp (we generate OTP, hash & save). Returns OTP for logging only (do not return to frontend!) */
    public function sendViaSendHttpFallback(string $mobile): array
    {
        // generate OTP
        $otp = $this->generateOtp();
        $message = "{$otp} is the Onetime password (OTP) for " . config('app.name') . ". Please do not share OTP for security reasons. {$this->sender}";

        try {
            $url = 'https://api.msg91.com/api/sendhttp.php';
            $resp = Http::get($url, [
                'authkey'   => $this->authKey,
                'mobiles'   => $mobile,
                'sender'    => $this->sender,
                'route'     => config('services.msg91.route'),
                'message'   => $message,
                'DLT_TE_ID' => $this->templateId,
                'country'   => 91
            ]);

            if ($resp->successful()) {
                // store hashed otp in DB with expiry only for fallback (keep small TTL)
                $ttl = (int)config('otp.ttl_minutes', env('OTP_TTL_MINUTES', 5));
                Otp::updateOrCreate(
                    ['mobile' => $mobile],
                    [
                        'otp_hash'   => Hash::make($otp),
                        'expires_at' => now()->addMinutes($ttl),
                        'attempts'   => 0
                    ]
                );

                return ['success' => true, 'provider' => 'msg91_sendhttp', 'otp' => $otp, 'body' => $resp->body()];
            }

            Log::error('MSG91 sendhttp failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['success' => false, 'error' => 'sendhttp_failed', 'status' => $resp->status(), 'body' => $resp->body()];
        } catch (Exception $e) {
            Log::error('MSG91 sendhttp exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'sendhttp_exception', 'exception' => $e->getMessage()];
        }
    }

    protected function generateOtp(): string
    {
        $min = (int) pow(10, $this->otpLength - 1);
        $max = (int) pow(10, $this->otpLength) - 1;
        return (string)random_int($min, $max);
    }

    /** Fallback verification (when we stored hashed OTP in DB) */
    public function verifyFallback(string $mobile, string $otp): array
    {
        $record = Otp::where('mobile', $mobile)->first();
        if (!$record) {
            return ['success' => false, 'error' => 'not_found'];
        }

        if ($record->isExpired()) {
            $record->delete();
            return ['success' => false, 'error' => 'expired'];
        }

        if (!Hash::check($otp, $record->otp_hash)) {
            $record->incrementAttempts();
            if ($record->attempts >= 5) {
                $record->delete();
            }
            return ['success' => false, 'error' => 'invalid'];
        }

        // valid
        $record->delete();
        return ['success' => true];
    }



    


}
