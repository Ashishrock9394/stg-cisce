<?php

namespace App\Jobs;

use App\Models\Sstudent;
use DB;
use App\Mail\StudentReportCardMail;
use App\Models\EmailReportBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailReportApiService;

class SendStudentReportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $studentId;
    public string $pdfPath;    
    public $report_batch;

    public $tries = 3;
    public $timeout = 300;

    public function __construct(int $studentId, string $pdfPath, $report_batch)
    {
        $this->studentId = $studentId;
        $this->pdfPath = $pdfPath;
        $this->report_batch = $report_batch;
    }

    public function handle(EmailReportApiService $emailService): void
    {
        try {

            $student = Sstudent::with('school')->find($this->studentId);

            if (!$student) {
                Log::warning('Student not found to email the report card', [
                    'student_id' => $this->studentId
                ]);
                return;
            }

            if (empty($student->email_id)) {
                Log::warning('Student email missing to send the report card', [
                    'student_id' => $this->studentId
                ]);
                return;
            }

            // by smtp 
            // Mail::to($student->email_id)
            //     ->send(new StudentReportCardMail(
            //         $student,
            //         $this->pdfPath
            //     ));

            /**
             * Via Infinito API
             * By Ashish 15 July 2026
             **/

            $emailService->sendReportCard($student, $this->pdfPath, $this->report_batch);

            EmailReportBatch::where('id', $this->report_batch)
                ->update([
                    'status' => 'sent',
                ]);


        } catch (\Throwable $e) {

            EmailReportBatch::where('id', $this->report_batch)
                ->update([
                    'status' => 'failed',
                ]);

            Log::error('Report card email failed', [
                'student_id' => $this->studentId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}