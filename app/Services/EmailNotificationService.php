<?php

namespace App\Services;

use App\Models\Sstudent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Throwable;

class EmailNotificationService
{
    public function sendNotification($user, string $subject, string $message, ?string $attachmentPath = null): bool
    {
        try {

            $attachment = null;

            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {

                $fullPath = Storage::disk('public')->path($attachmentPath);

                $attachment = [
                    "content"     => base64_encode(file_get_contents($fullPath)),
                    "filename"    => basename($fullPath),
                    "type"        => mime_content_type($fullPath),
                    "disposition" => "attachment"
                ];
            }

            $payload = [
                "apiver" => "1.0",
                "email" => [
                    "ver" => "1.0",
                    "dlr" => [
                        "url" => ""
                    ],
                    "messages" => [
                        [
                            "id" => uniqid('notification_'),
                            "addresses" => [
                                [
                                    "address_seq_id" => uniqid(),
                                    "to" => [
                                        [
                                            "emailid" => $user->email,
                                            "name"    => $user->name,
                                        ]
                                    ],
                                    "personalization_data" => [
                                        "username"     => $user->name,
                                        "emailid"      => $user->email,
                                        "subject"      => $subject,
                                        "message"      => $message,
                                        "school_code"  => $user->school_code,
                                        "school_name"  => $user->school_name,
                                        "contact_us"   => "https://active.cisce.org/contact-us",
                                        "site_url"     => "https://active.cisce.org",
                                        "logo"         => "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                        "today"        => now()->format('d M Y, h:i A'),
                                    ],
                                    "attachments" => $attachment ? [$attachment] : [],
                                ]
                            ],
                            "subject" => $subject,
                            "content" => [
                                [
                                    "template_id" => "fa4f970255a92998"
                                ]
                            ],
                            "from" => [
                                "emailid" => "noreply@activeconnect.cisce.org",
                                "name"    => "Active CISCE"
                            ],
                            "category" => "CISCE Notification"
                        ]
                    ]
                ]
            ];

            Log::info('Sending Notification Email', [
                'user_id'    => $user->id,
                'email'      => $user->email,
                'provider'   => 'Email API',
            ]);

            $response = Http::withToken(config('services.email_api.token'))
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(60)
                ->post(config('services.email_api.url'), $payload);

            if (!$response->successful()) {

                Log::info('Sending Notification Email failed', [
                    'user_id' => $user->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);

                throw new \Exception('MAIL_FAILED');
            }

            return true;

        } catch (Throwable $e) {

            Log::error('Sending Notification Email API Exception', [
                'user_id' => $user->id,
                'message'    => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            throw new \Exception('MAIL_FAILED');
        }
    }
}