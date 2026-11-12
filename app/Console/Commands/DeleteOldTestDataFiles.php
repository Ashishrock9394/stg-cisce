<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteOldTestDataFiles extends Command
{
    protected $signature = 'files:delete-old-test-files';
    protected $description = 'Delete files older than 8 days from Azure storage';

    public function handle()
    {
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Delete upload test files older than 8 days from Azure storage.');
        $disk = Storage::disk('azure');
        $directory = 'import_tests/';
        $threshold = Carbon::now()->subDays(8);

        $files = $disk->allFiles($directory);

        foreach ($files as $file) {
            // Skip folders just in case
            if ($disk->exists($file)) {
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

                if ($lastModified->lt($threshold)) {
                    $disk->delete($file);
                    $this->info("Deleted: {$file}");
                }
            }
        }

        $this->info('Old test files cleanup completed.');

        $errorDir = 'test_errors/';

        $errorFiles = $disk->allFiles($errorDir);

        foreach ($errorFiles as $file) {
            // Skip folders just in case
            if ($disk->exists($file)) {
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

                if ($lastModified->lt($threshold)) {
                    $disk->delete($file);
                    $this->info("Deleted: {$file}");
                }
            }
        }

        $emailDir = 'import_emails/';

        $emailFiles = $disk->allFiles($emailDir);

        foreach ($emailFiles as $file) {
            // Skip folders just in case
            if ($disk->exists($file)) {
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

                if ($lastModified->lt($threshold)) {
                    $disk->delete($file);
                    $this->info("Deleted: {$file}");
                }
            }
        }

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Delete upload test files cleanup completed');
        return Command::SUCCESS;
    }
}
