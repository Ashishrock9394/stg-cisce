<?php

namespace App\Jobs;

use App\Mail\StudentEmailImportCompletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendStudentEmailImportCompletedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function handle()
    {
        $batch = DB::table('email_batch')
            ->where('id', $this->batchId)
            ->first();

        if (!$batch || $batch->notification_sent) {
            return;
        }

        $school = DB::table('school_reference')
            ->join('users', 'users.id', 'school_reference.school_user_id')
            ->join('schools', 'schools.id', 'school_reference.school_id')
            ->select('users.email', 'schools.school_name')
            ->where('school_reference.school_id', $batch->school_id)
            ->first();

        if (!$school || empty($school->email)) {
            return;
        }

        // Mail::to($school->email)
        //     ->send(
        //         new StudentEmailImportCompletedMail(
        //             $school,
        //             $batch
        //         )
        //     );

        DB::table('email_batch')
            ->where('id', $batch->id)
            ->update([
                'notification_sent' => '1',
                'updated_at' => now()
            ]);
    }
}