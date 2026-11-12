<?php

namespace App\Services;

use App\Models\Sstudent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\EmailReportBatch;
use App\Models\EmailReportRequest;
use Throwable;

class EmailReportApiService
{
    public function sendReportCard(Sstudent $student, string $pdfPath, $report_batch): bool
    {
        try {

            if (!File::exists($pdfPath)) {
                throw new \Exception("PDF file not found: {$pdfPath}");
            }

            $email = trim($student->email_id ?? '');
            
            if (empty($email)) {
                EmailReportRequest::where('student_id', $student->id)
                    ->where('batch_id', $report_batch)
                    ->update([
                        'status' => 'failed',
                        'description' => 'Email address is missing.',
                    ]);

                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                EmailReportRequest::where('student_id', $student->id)
                    ->where('batch_id', $report_batch)
                    ->update([
                        'status' => 'failed',
                        'description' => "Invalid email format: {$email}.",
                    ]);

                return false;
            }

            $domain = strtolower(substr(strrchr($email, '@'), 1));

            if (!checkdnsrr($domain, 'MX')) {
                EmailReportRequest::where('student_id', $student->id)
                    ->where('batch_id', $report_batch)
                    ->update([
                        'status' => 'failed',
                        'description' => "Invalid email domain '{$domain}'. The domain does not have a valid mail server.",
                    ]);

                return false;
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
                            "id" => uniqid('report_'),
                            "addresses" => [
                                [
                                    "address_seq_id" => uniqid(),
                                    "to" => [
                                        [
                                            "emailid" => $email,
                                            "name"    => $student->student_name,
                                        ]
                                    ],
                                    "personalization_data" => [
                                        "student_name" => $student->student_name,
                                        "emailid"      => $email,
                                        "username"     => $student->user_id,
                                        "academic_year" => $student->academic_year,
                                        "school_name"     => $student->school->school_name,
                                        "contact_us"   => "https://active.cisce.org/contact-us",
                                        "site_url"     => "https://active.cisce.org",
                                        "logo"         => "https://active.cisce.org/logo/fitness365-logo-web.jpg",
                                        "today"        => now()->format('d M Y, h:i A'),
                                    ],
                                    "attachments" => [
                                        [
                                            "content"     => base64_encode(File::get($pdfPath)),
                                            "filename"    => basename($pdfPath),
                                            "type"        => "application/pdf",
                                            "disposition" => "attachment"
                                        ]
                                    ]
                                ]
                            ],
                            "subject" => "Active CISCE Fitness Report Card - {$student->user_id}",
                            "content" => [
                                [
                                    "template_id" => "719d2ab56a5b34f3"
                                ]
                            ],
                            "from" => [
                                "emailid" => "noreply@activeconnect.cisce.org",
                                "name"    => "Active CISCE"
                            ],
                            "reply_to" => [
                                "emailid" => "",
                                "name"    => "Active CISCE"
                            ],
                            "category" => "Report Card"
                        ]
                    ]
                ]
            ];

            $response = Http::withToken(config('services.email_api.token'))
                ->acceptJson()
                ->contentType('application/json')
                ->timeout(60)
                ->post(config('services.email_api.url'), $payload);

            if (!$response->successful()) {

                Log::error('Report Card Email API Failed', [
                    'student_id' => $student->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
                EmailReportRequest::where('student_id', $student->id)
                    ->where('batch_id', $report_batch)
                    ->update([
                        'status' => 'failed',
                        'description' => 'Email API request failed with HTTP status '
                            . $response->status(),
                    ]);

                throw new \Exception('MAIL_FAILED');
            }else{
                EmailReportBatch::where('id', $report_batch)->increment('completed_students');
                EmailReportRequest::where('student_id', $student->id)
                ->where('batch_id', $report_batch)
                ->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                
            }

            return true;

        } catch (Throwable $e) {
            Log::error('Report Card Email API Exception', [
                'student_id' => $student->id,
                'batch_id'   => $report_batch,
                'message'    => $e->getMessage(),
            ]);

            EmailReportRequest::where('student_id', $student->id)
                ->where('batch_id', $report_batch)
                ->where('status', '!=', 'sent')
                ->update([
                    'status' => 'failed',
                    'description' => $e->getMessage(),
                ]);

            throw new \Exception('MAIL_FAILED', 0, $e);
        }
    }
}