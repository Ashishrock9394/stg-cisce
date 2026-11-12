<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AcsEmailService
{
    protected string $endpoint;
    protected string $accessKey;
    protected string $senderAddress;

    public function __construct()
    {
        $this->endpoint = rtrim(config('services.acs.endpoint'), '/');
        $this->accessKey = config('services.acs.access_key');
        $this->senderAddress = config('services.acs.sender_address', 'DoNotReply@yourdomain.com');
        // $this->senderAddress = 'noreply@active.cisce.org';
    }

    public function send(array $to, string $subject, string $html, string $plainText = null)
    {
        try {
            $path = '/emails:send?api-version=2023-03-31';
            
            // Extract host from endpoint (remove protocol)
            $host = parse_url($this->endpoint, PHP_URL_HOST);
            
            // Prepare payload
            $payload = [
                "senderAddress" => $this->senderAddress,
                "content" => [
                    "subject" => $subject,
                    "html" => $html,
                    "plainText" => $plainText ?? strip_tags($html)
                ],
                "recipients" => [
                    "to" => array_map(function ($email) {
                        return ["address" => $email];
                    }, $to)
                ]
            ];

            $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
            $contentLength = strlen($json);
            $contentHash = base64_encode(hash('sha256', $json, true));
            $date = gmdate('D, d M Y H:i:s \G\M\T');
            
            // String to sign - MUST include host header
            $stringToSign = "POST\n" . 
                           "/emails:send?api-version=2023-03-31\n" .
                           "x-ms-date:" . $date . "\n" .
                           "host:" . $host . "\n" .
                           "x-ms-content-sha256:" . $contentHash;
            // $stringToSign = "POST\n" .
            //                 $path . "\n" .
            //                 "x-ms-date:" . $date . "\n" .
            //                 "host:" . $host . "\n" .
            //                 "x-ms-content-sha256:" . $contentHash;
            // Sign the string
            $signature = base64_encode(
                hash_hmac('sha256', $stringToSign, base64_decode($this->accessKey), true)
            );
            
            // Build authorization header
            $authorization = "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=" . $signature;

            // Make the request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => $contentLength,
                'x-ms-date' => $date,
                'x-ms-content-sha256' => $contentHash,
                'Authorization' => $authorization,
                'Host' => $host,
            ])
            ->withBody($json, 'application/json')
            ->post($this->endpoint . $path);

            Log::info('ACS Email Payload:', $payload);
            Log::info('ACS Email JSON:', ['json' => $json]);
            Log::info('ACS Email StringToSign:', ['stringToSign' => $stringToSign]);
            Log::info('ACS Email Authorization:', ['authorization' => $authorization]);

            // Check response
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->header('x-ms-request-id'),
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Azure ACS Email Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // Helper method to send to multiple recipients
    public function sendToMultiple(array $recipients, string $subject, string $html, string $plainText = null)
    {
        $toArray = [];
        foreach ($recipients as $email) {
            $toArray[] = ['address' => $email];
        }

        try {
            $path = '/emails:send?api-version=2023-03-31';
            $host = parse_url($this->endpoint, PHP_URL_HOST);
            
            $payload = [
                "senderAddress" => $this->senderAddress,
                "content" => [
                    "subject" => $subject,
                    "html" => $html,
                    "plainText" => $plainText ?? strip_tags($html)
                ],
                "recipients" => [
                    "to" => $toArray
                ]
            ];

            $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
            $contentLength = strlen($json);
            $contentHash = base64_encode(hash('sha256', $json, true));
            $date = gmdate('D, d M Y H:i:s \G\M\T');
            
            $stringToSign = "POST\n" . 
                           "/emails:send?api-version=2023-03-31\n" .
                           "x-ms-date:" . $date . "\n" .
                           "host:" . $host . "\n" .
                           "x-ms-content-sha256:" . $contentHash;
            
            $signature = base64_encode(
                hash_hmac('sha256', $stringToSign, base64_decode($this->accessKey), true)
            );
            
            $authorization = "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=" . $signature;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => $contentLength,
                'x-ms-date' => $date,
                'x-ms-content-sha256' => $contentHash,
                'Authorization' => $authorization,
                'Host' => $host,
            ])
            ->withBody($json, 'application/json')
            ->post($this->endpoint . $path);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Azure ACS Bulk Email Error: ' . $e->getMessage());
            return false;
        }
    }
}