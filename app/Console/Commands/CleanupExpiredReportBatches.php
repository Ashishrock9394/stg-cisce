<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportBatch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupExpiredReportBatches extends Command
{
    protected $signature = 'reports:cleanup';

    protected $description = 'Check And Clean Generted Report at regular';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle() {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Check And Clean Generted Report at regular from Azure.');

        $disk = Storage::disk('azure');
        $expiry_date = 7;
         ReportBatch::whereNotNull('expires_at')->where('expires_at', '<', now())->whereNotNull('final_zip_path')
            
            ->chunkById(50, function ($batches) use ($disk) {
                foreach ($batches as $batch) {

                    $zipPath = 'excel_storage/reports/' . $batch->final_zip_path;
                    $batchDirectory = dirname($zipPath);

                    $files = $disk->allFiles($batchDirectory);
                    foreach ($files as $file) {
                        $disk->delete($file);
                        $this->info("Deleted: {$file}");
                    }

                    $directories = $disk->allDirectories($batchDirectory);
                    foreach ($directories as $dir) {
                        $disk->deleteDirectory($dir);
                    }

                    $batch->delete();
                }
            });

        $this->info('Old reports cleanup completed successfully');
        return Command::SUCCESS;
    }

}
