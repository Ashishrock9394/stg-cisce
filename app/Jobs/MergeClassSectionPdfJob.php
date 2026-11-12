<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use App\Notifications\ReportReadyNotification;
use App\Models\School;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class MergeClassSectionPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $schoolId;
    public int $term_id;
    public int $classId;
    public string $sectionId;
    public int $report_batch_id;
    public $timeout = 900;
    public $tries = 3;
    public int $expectedStudentCount;

    public function __construct(int $schoolId, int $term_id, int $classId, string $sectionId, int $report_batch_id, int $expectedStudentCount)
    {
        $this->schoolId = $schoolId;
        $this->term_id = $term_id;
        $this->classId = $classId;
        $this->sectionId = $sectionId;
        $this->report_batch_id = $report_batch_id;
        $this->expectedStudentCount = $expectedStudentCount;
    }


    public function handle()  {

        $disk = Storage::disk('reports');

        $baseRelativePath = "{$this->schoolId}/batch_{$this->report_batch_id}/class_{$this->classId}/section_{$this->sectionId}";
        $mergedPdfRelativePath = "{$this->schoolId}/batch_{$this->report_batch_id}/class_{$this->classId}/section_{$this->sectionId}.pdf";

        if (!$disk->exists($baseRelativePath)) {
            Log::warning('Merge skipped, directory not found', ['path' => $baseRelativePath]);
            return;
        }

        // FPDI needs absolute paths
        $baseAbsolutePath = $disk->path($baseRelativePath);
        $mergedPdfAbsolutePath = $disk->path($mergedPdfRelativePath);

        $pdf = new Fpdi();

        // Get student PDFs (relative paths)
        $files = collect($disk->files($baseRelativePath))
            ->filter(fn ($file) => str_starts_with(basename($file), 'Fitness_'))
            ->sort()
            ->values();

        if ($files->isEmpty()) {
            Log::warning('No student PDFs found', ['path' => $baseRelativePath]);
            return;
        }

        foreach ($files as $relativeFile) {

            $absoluteFile = $disk->path($relativeFile);

            $pageCount = $pdf->setSourceFile($absoluteFile);

            for ($page = 1; $page <= $pageCount; $page++) {
                $tplIdx = $pdf->importPage($page);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx);
            }
        }

        // Save merged PDF
        $pdf->Output($mergedPdfAbsolutePath, 'F');

        // Cleanup student PDFs + directory
        foreach ($files as $file) {
            $disk->delete($file);
        }

        $disk->deleteDirectory($baseRelativePath);

        // Update batch progress
        $batch = \App\Models\ReportBatch::find($this->report_batch_id);

        if ($batch) {
            $batch->increment('completed_students', $this->expectedStudentCount);
            $this->checkAndSendNotification($batch);
        }
    }


    protected function CheckAndSendNotification($batch) {

        if ($batch->completed_students >= $batch->total_students) {
            $batch->update(['status' => 'completed']);
            
            $batchFolderRelative = "{$this->schoolId}/batch_{$this->report_batch_id}";
            $zipRelativePath = $this->createZipAndDownload($batchFolderRelative);



            $azurePath = "excel_storage/reports/{$zipRelativePath}";
            Storage::disk('azure')->put($azurePath, fopen(Storage::disk('reports')->path($zipRelativePath), 'r') );


            $downloadUrl = route('report.download.permanent', [
                'batchId' => $this->report_batch_id,
            ]);

            $batch->update([
                'final_zip_path' => $zipRelativePath, 
                'download_path'  => $downloadUrl,
                'expires_at' => now()->addDays(7),
            ]);

            Storage::disk('reports')->delete($zipRelativePath);
        }
    }
    


    private function createZipAndDownload(string $batchFolderRelative) {

        $disk = Storage::disk('reports');

        $zipFileName = "Report_{$this->report_batch_id}_{$this->schoolId}_" . now()->format('Ymd_His') . ".zip";
        $zipRelativePath = "{$batchFolderRelative}/{$zipFileName}";
        $zipAbsolutePath = $disk->path($zipRelativePath);

        $zip = new \ZipArchive();

        if ($zip->open($zipAbsolutePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        foreach ($disk->allFiles($batchFolderRelative) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $zip->addFile(
                    $disk->path($file),
                    str_replace($batchFolderRelative . '/', '', $file)
                );
            }
        }

        $zip->close();
        foreach ($disk->directories($batchFolderRelative) as $dir) {
            $disk->deleteDirectory($dir);
        }

        foreach ($disk->files($batchFolderRelative) as $file) {
            if (!str_ends_with($file, $zipFileName)) {
                $disk->delete($file);
            }
        }

        return $zipRelativePath; 
    }

    private function deleteAllExceptZip($directory, $zipFileName) {

        $items = new \FilesystemIterator($directory);
        foreach ($items as $item) {
            if ($item->isDir()) {
                $this->deleteDirectory($item->getPathname());
            } elseif ($item->isFile() && $item->getFilename() !== $zipFileName) {
                @unlink($item->getPathname());
            }
        }
    }

    private function deleteDirectory($dir) {
        
        if (!is_dir($dir)) return;

        $files = new \FilesystemIterator($dir);
        foreach ($files as $file) {
            if ($file->isDir()) {
                $this->deleteDirectory($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }
        @rmdir($dir);
    }

}
