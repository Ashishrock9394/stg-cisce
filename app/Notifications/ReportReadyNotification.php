<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReportReadyNotification extends Notification
{
    use Queueable;

    protected $schoolId;
    protected $classId;
    protected $section;
    protected $filePath;

    public function __construct($schoolId, $classId, $section, $filePath) {

        $this->schoolId = $schoolId;
        $this->classId = $classId;
        $this->section = $section;
        $this->filePath = $filePath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the datbase representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => "Report for Class {$this->classId} is ready.",
            'file_path' => $this->filePath,
            'school_id' => $this->schoolId,
            'class_id' => $this->classId,
            'section' => $this->section,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
