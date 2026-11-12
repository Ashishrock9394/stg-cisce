<?php

namespace App\Services;
use App\Contracts\EmailServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\View\Factory as ViewFactory;


class SmtpEmailService implements EmailServiceInterface {

    public function send(array $recipient, string $subject, string $htmlContent, string $textContent = '')  {

        Mail::send([], [], function ($message) use ($recipient, $subject, $htmlContent, $textContent) {
            $message->to($recipient['email'], $recipient['name'] ?? null)
                ->subject($subject)
                ->setBody($htmlContent, 'text/html');

            if ($textContent) {
                $message->addPart($textContent, 'text/plain');
            }
        });
    }
}