<?php

namespace App\Jobs;


use App\Imports\ImportFMSTestData;
use App\Imports\ImportFitnessTestData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Storage;

class TestDataImportJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $schoolId;
    protected $action;
    protected $userId;
    protected $skillId;
    protected $filePath;
    protected $logId;

    public function __construct($schoolId, $action, $userId, $skillId, $filePath, $logId) {

        $this->schoolId = $schoolId;
        $this->action = $action;
        $this->userId = $userId;
        $this->skillId = $skillId;
        $this->filePath = $filePath;
        $this->logId = $logId;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $log = \App\Models\TestImportLog::find($this->logId);
        $log?->update(['status' => 'processing']);
        
        try {
            $importData = ($this->skillId >= 1 && $this->skillId <= 15)
					? new ImportFMSTestData($this->schoolId, $this->action, $this->userId, $this->skillId, $this->logId)
					: new ImportFitnessTestData($this->schoolId, $this->action, $this->userId, $this->skillId, $this->logId);

			// Excel::import($importData, $this->filePath);  // for local
            Excel::import($importData, $this->filePath, 'azure');


        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

   
    }
}
