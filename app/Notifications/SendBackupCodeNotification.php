<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendBackupCodeNotification extends Notification
{
    use Queueable;

    protected $backupCode;  
    protected $user;

    public function __construct($backupCode, $user) {

       $this->backupCode = $backupCode;
       $this->user = $user;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Password Updated Successfully')
            ->markdown('emails.backup_code', [
                'backupCode' => $this->backupCode,
                'user' => $this->user,
            ]);
    }    
}
