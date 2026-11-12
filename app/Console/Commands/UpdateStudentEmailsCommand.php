<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendStudentEmailImportCompletedJob;

class UpdateStudentEmailsCommand extends Command
{
    protected $signature = 'students:update-emails';

    protected $description = 'Update student emails from students_email table';

    public function handle()
    {
        $schoolIds = DB::table('email_batch')
            ->where('status', 'processing')
            ->distinct()
            ->pluck('school_id');

        if ($schoolIds->isEmpty()) {
            $this->info('No pending batches found.');
            return Command::SUCCESS;
        }

        foreach ($schoolIds as $schoolId) {

            $records = DB::table('students_email')
                ->where('school_id', $schoolId)
                ->where('status', 'pending')
                ->get();

            if ($records->isEmpty()) {
                continue;
            }
            $success = 0;
            $failed = 0;
            $batchCounts = [];

            foreach ($records as $record) {

                try {

                    $updated = DB::table('students')
                        ->where('id', $record->student_id)
                        ->where('school_id', $record->school_id)
                        ->update([
                            'email_id' => $record->email,
                            'updated_at' => now()
                        ]);

                    if ($updated) {

                        DB::table('students_email')
                            ->where('id', $record->id)
                            ->update([
                                'status' => 'updated',
                                'updated_at' => now()
                            ]);

                        $success++;

                        $batchCounts[$record->batch_id]['success'] =
                            ($batchCounts[$record->batch_id]['success'] ?? 0) + 1;

                    } else {

                        DB::table('students_email')
                            ->where('id', $record->id)
                            ->update([
                                'status' => 'failed',
                                'error_message' => 'Student not found',
                                'updated_at' => now()
                            ]);

                        $failed++;

                        $batchCounts[$record->batch_id]['failed'] =
                            ($batchCounts[$record->batch_id]['failed'] ?? 0) + 1;
                    }

                } catch (\Exception $e) {

                    DB::table('students_email')
                        ->where('id', $record->id)
                        ->update([
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                            'updated_at' => now()
                        ]);

                    $failed++;

                    $batchCounts[$record->batch_id]['failed'] =
                        ($batchCounts[$record->batch_id]['failed'] ?? 0) + 1;
                }
            }

            foreach ($batchCounts as $batchId => $counts) {

                DB::table('email_batch')
                    ->where('id', $batchId)
                    ->increment('success_count', $counts['success'] ?? 0);

                DB::table('email_batch')
                    ->where('id', $batchId)
                    ->increment('failed_count', $counts['failed'] ?? 0);

                $batch = DB::table('email_batch')
                    ->where('id', $batchId)
                    ->first();

                if (
                    ($batch->success_count + $batch->failed_count)
                    >= $batch->total_students
                ) {
                    DB::table('email_batch')
                        ->where('id', $batchId)
                        ->update([
                            'status' => 'completed',
                            'updated_at' => now()
                        ]);

                    SendStudentEmailImportCompletedJob::dispatch($batchId)->onQueue('import_emails');

                } else {
                    DB::table('email_batch')
                        ->where('id', $batchId)
                        ->update([
                            'status' => 'processing',
                            'updated_at' => now()
                        ]);
                }
            }

            $this->info("Email Updated for School {$schoolId} => Success: {$success}, Failed: {$failed}");
        }

        return Command::SUCCESS;
    }
}