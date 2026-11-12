<?php

namespace App\Jobs;

use Throwable;
use App\Imports\ImportStudentProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Batchable;
use App\Models\StudentImportLog;

class ProcessStudentImport implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $schoolId;
    protected $action;
    protected $userId;
    protected $filePath;
    protected $logId;

    public function __construct($schoolId, $action, $userId, $filePath, $logId) {

        $this->schoolId = $schoolId;
        $this->action = $action;
        $this->userId = $userId;
        $this->filePath = $filePath;
        $this->logId = $logId;
    }


    public function uniqueId() {
        return 'student-import-' . $this->schoolId;
    }


    public function handle() {

        $log = StudentImportLog::find($this->logId);
        
        try {

            DB::table('student_import_status')
            ->where('school_id', $this->schoolId)
            ->update([
                'status' => 'processing',
                'updated_at' => now(),
            ]);
            $log?->update(['status' => 'processing']);

            $import = new ImportStudentProfile($this->schoolId, $this->action, $this->userId, $this->logId);
            Excel::import($import, storage_path('app/' . $this->filePath));

              DB::table('student_import_status')
                ->where('school_id', $this->schoolId)
                ->update([
                    'status' => 'idle',
                    'updated_at' => now(),
                ]);

        } catch (\Throwable $e) {

            DB::table('student_import_status')
                ->where('school_id', $this->schoolId)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);

            $log?->update([
                'status' => 'failed',
                'message' => 'Something went wrong: '. $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error(
                'Student import failed for school ID '
                . $this->schoolId
                . '. Error: '
                . $e->getMessage()
            );

            throw $e;
        }

        // Excel::import($this->importData, storage_path('app/' . $this->filePath));
   
    }

    public function failed(Throwable $e) {

        DB::table('student_import_status')
            ->where('school_id', $this->schoolId)
            ->update([
                'status' => 'failed',
                'updated_at' => now(),
            ]);

        StudentImportLog::where('id', $this->logId)
            ->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

        Log::error(
            'Queue permanently failed for school ID '
            . $this->schoolId
            . '. Error: '
            . $e->getMessage()
        );
    }
    
}
