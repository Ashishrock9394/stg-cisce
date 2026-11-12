<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteUploadHistory extends Command
{
    protected $signature = 'delete:upload-history';

    protected $description = 'Delete upload history older than defined retention periods';

    public function handle()
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Delete upload history from DB scheduler starts');
        $testDays = 7;
        $studentDays = 15;
        
        $studentDeleted = DB::table('student_import_logs')
            ->where('created_at', '<', Carbon::now()->subDays($studentDays))
            ->delete();

        $testDeleted = DB::table('test_import_logs')
            ->where('created_at', '<', Carbon::now()->subDays($testDays))
            ->update(['is_active' => 'inactive']);

        $emailDeleted = DB::table('email_batch')
            ->where('created_at', '<', Carbon::now()->subDays($testDays))
            ->delete();

        $reportBatch = DB::table('report_batches')
            ->where('created_at', '<', Carbon::now()->subDays($testDays))
            ->delete();

        $report_requests = DB::table('report_requests')
            ->where('created_at', '<', Carbon::now()->subDays($testDays))
            ->delete();

        $this->info("Deleted {$studentDeleted} student import logs older than {$studentDays} days.");
        $this->info("Deleted {$testDeleted} test import logs older than {$testDays} days.");
        $this->info("Deleted {$emailDeleted} parent email update logs older than {$testDays} days.");
        $this->info("Deleted {$reportBatch} report request logs older than {$testDays} days.");
    }
}
