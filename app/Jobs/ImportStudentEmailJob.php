<?php

namespace App\Jobs;

use App\Imports\StudentEmailImport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportStudentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchId;
    public $schoolId;
    public $filePath;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function handle()
    {
        try {

            $batch = DB::table('email_batch')
                ->where('id', $this->batchId)
                ->where('status', 'pending')
                ->first();

            if (!$batch) {
                return;
            }

            DB::table('email_batch')
                ->where('id', $this->batchId)
                ->update([
                    'status' => 'processing',
                    'updated_at' => now(),
                ]);

            Excel::import(
                new StudentEmailImport(
                    $batch->id,
                    $batch->school_id
                ),
                $batch->file_path,
                'azure'
            );

        } catch (\Exception $e) {

            DB::table('email_batch')
                ->where('id', $this->batchId)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now()
                ]);

            throw $e;
        }
    }
}