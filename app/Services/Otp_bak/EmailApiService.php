<?php

namespace App\Services\Otp;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailApiService
{
    // for sending login otp

    public function sendOtp(User $user, string $otp): bool
    {   
        try{
            $payload = [
                "apiver" => "1.0",
                "email" => [
                    "ver" => "1.0",
                    "dlr" => [
                        "url" => ""
                    ],
                    "messages" => [
                        [
                            "id" => uniqid('otp_'),
                            "addresses" => [
                                [
                                    "address_seq_id" => uniqid(),
                                    "to" => [
                                        [
                                            "emailid" => $user->email,
                                            "name" => $user->name,
                                        ]
                                    ],
                                    "personalization_data" => [
                                        "name" => $user->name,
                                        "emailid" => $user->email,
                                        "otp" => $otp,
                                        "contact_us"=> "https://active.cisce.org/contact-us",
                                        "site_url"=> "https://active.cisce.org",
                                        "logo"=> "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                        "today" => now()->format('d M Y, h:i A'),
                                    ]
                                ]
                            ],
                            "subject" => "Active CISCE Login Authentication – Verify Your Login",
                            "content" => [
                                [
                                    "template_id" => "9ddf64adbabaed9a"
                                ]
                            ],
                            "from" => [
                                "emailid" => "noreply@notifyactive.cisce.org",
                                "name" => "Active CISCE"
                            ],
                            "reply_to" => [
                                "emailid" => "",
                                "name" => "Active CISCE"
                            ],
                            "category" => "OTP"
                        ]
                    ]
                ]
            ];

            $response = Http::withToken(config('services.email_api.token'))
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(30)
                ->post(config('services.email_api.url'), $payload);

            if (!$response->successful()) {

                Log::error('Email API Failed', [
                    'user_id' => $user->id,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);

                throw new \Exception('MAIL_FAILED');
            }
            return true;

        } catch (\Exception $e) {

            Log::error('Email API Exception', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('MAIL_FAILED');
        }
    }

    // sending password reset link 

    public function sendPasswordResetLink(User $user, string $token): bool
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        Log::info('Reset Link' . $resetUrl);

        $payload = [
            "apiver" => "1.0",
            "email" => [
                "ver" => "1.0",
                "messages" => [
                    [
                        "id" => uniqid('reset_'),
                        "addresses" => [
                            [
                                "address_seq_id" => uniqid(),
                                "to" => [
                                    [
                                        "emailid" => $user->email,
                                        "name" => $user->name,
                                    ]
                                ],
                                "personalization_data" => [
                                    "name" => $user->name,
                                    "emailid" => $user->email,
                                    "resetUrl" => $resetUrl,
                                    "contact_us"=> "https://active.cisce.org/contact-us",
                                    "site_url"=> "https://active.cisce.org",
                                    "logo"=> "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                    "today" => now()->format('d M Y, h:i A'),
                                ]
                            ]
                        ],
                        "subject" => "Reset Your Active CISCE Portal Password",
                        "content" => [
                            [
                                "template_id" => "fc0d0b078ed4b83a"
                            ]
                        ],
                        "from" => [
                            "emailid" => "noreply@activeconnect.cisce.org",
                            "name" => "Active CISCE"
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken(config('services.email_api.token'))
            ->acceptJson()
            ->post(config('services.email_api.url'), $payload);

        return $response->successful();
    }

    // Sending password reset otp 

    public function sendPasswordResetOtp(User $user, string $otp): bool
    {   
        try{
            $payload = [
                "apiver" => "1.0",
                "email" => [
                    "ver" => "1.0",
                    "dlr" => [
                        "url" => ""
                    ],
                    "messages" => [
                        [
                            "id" => uniqid('otp_'),
                            "addresses" => [
                                [
                                    "address_seq_id" => uniqid(),
                                    "to" => [
                                        [
                                            "emailid" => $user->email,
                                            "name" => $user->name,
                                        ]
                                    ],
                                    "personalization_data" => [
                                        "name" => $user->name,
                                        "emailid" => $user->email,
                                        "otp" => $otp,
                                        "contact_us"=> "https://active.cisce.org/contact-us",
                                        "site_url"=> "https://active.cisce.org",
                                        "logo"=> "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                        "today" => now()->format('d M Y, h:i A'),
                                    ]
                                ]
                            ],
                            "subject" => "Active CISCE Login Authentication – Verify Your Login",
                            "content" => [
                                [
                                    "template_id" => "3a1257d66ffc28c5"
                                ]
                            ],
                            "from" => [
                                "emailid" => "noreply@notifyactive.cisce.org",
                                "name" => "Active CISCE"
                            ],
                            "reply_to" => [
                                "emailid" => "",
                                "name" => "Active CISCE"
                            ],
                            "category" => "OTP"
                        ]
                    ]
                ]
            ];

            $response = Http::withToken(config('services.email_api.token'))
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(30)
                ->post(config('services.email_api.url'), $payload);

            if (!$response->successful()) {

                Log::error('Email API Failed', [
                    'user_id' => $user->id,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);

                throw new \Exception('MAIL_FAILED');
            }

            return true;

        } catch (\Exception $e) {

            Log::error('Email API Exception', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('MAIL_FAILED');
        }
    }

    // for sending backup code 

    public function sendBackupCode(User $user, string $backupCode): bool
    {
        try {

            $payload = [
                "apiver" => "1.0",
                "email" => [
                    "ver" => "1.0",
                    "messages" => [
                        [
                            "id" => uniqid('backup_'),
                            "addresses" => [
                                [
                                    "address_seq_id" => uniqid(),
                                    "to" => [
                                        [
                                            "emailid" => $user->email,
                                            "name" => $user->name,
                                        ]
                                    ],
                                    "personalization_data" => [
                                        "name" => $user->name,
                                        "emailid" => $user->email,
                                        "backupCode" => $backupCode,
                                        "contact_us"=> "https://active.cisce.org/contact-us",
                                        "site_url"=> "https://active.cisce.org",
                                        "logo"=> "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                        "today" => now()->format('d M Y, h:i A'),
                                    ]
                                ]
                            ],
                            "subject" => "Password Updated Successfully",
                            "content" => [
                                [
                                    "template_id" => "f75448901ec05650"
                                ]
                            ],
                            "from" => [
                                "emailid" => "noreply@activeconnect.cisce.org",
                                "name" => "Active CISCE"
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken(config('services.email_api.token'))
                ->acceptJson()
                ->post(config('services.email_api.url'), $payload);

            if (! $response->successful()) {
                throw new Exception('MAIL_FAILED');
            }

            return true;

        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}