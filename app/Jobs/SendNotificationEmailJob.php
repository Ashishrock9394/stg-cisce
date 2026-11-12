<?php

namespace App\Jobs;

use App\Services\EmailNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $subject;
    public $message;
    public $attachmentPath;

    public $tries = 3;

    public $timeout = 120;

    public function __construct($user, string $subject, string $message, ?string $attachmentPath = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
        $this->attachmentPath = $attachmentPath;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailNotificationService $emailService): void
    {
        try{
            /*
            $emailService->sendNotification(
                $this->user,
                $this->subject,
                $this->message,
                $this->attachmentPath
            );
            */
            
        }catch (\Throwable $e) {

            Log::error('Notification email job failed.', [
                'user_id' => $this->user->id,
                'email'   => $this->user->email,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
        
    }

}