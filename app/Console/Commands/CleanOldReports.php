<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanOldReports extends Command
{
    protected $signature = 'localreport:delete';
    protected $description = 'Delete reports older than 7 Days';

    public function handle() {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Local Report Storage Cleanup start.');

        $path = storage_path('app/public/reports');

        if (!File::isDirectory($path)) {
            $this->error("Reports directory does not exist: {$path}");

            return self::FAILURE;
        }else{            
            $cutoff = now()->subDays(7)->timestamp;
            foreach (File::directories($path) as $directory) {
                $modifiedAt = File::lastModified($directory);

                if ($modifiedAt < $cutoff) {
                    File::deleteDirectory($directory);

                    $this->info("Deleted: {$directory}");
                }
            }
            $this->info('[' . now()->format('Y-m-d H:i:s') . '] Local Report deleted.');
        }


        return Command::SUCCESS;
    }
}