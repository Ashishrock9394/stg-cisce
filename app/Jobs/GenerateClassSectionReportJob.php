<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\ReportHelperTrait;

use App\Jobs\MergeClassSectionPdfJob;
use Intervention\Image\Facades\Image;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;


class GenerateClassSectionReportJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue, Batchable, ReportHelperTrait;

    public int $schoolId;
    public int $classId;
    public string $section;
    public array $studentIds;
    public int $termId;
    public int $report_batch;
    public string $action;
    public $timeout = 1800;
    public $tries = 3;

    
    public function __construct(int $schoolId, int $classId, string $section, array $studentIds, int $termId, int $report_batch, string $action) {

        $this->schoolId = $schoolId;
        $this->classId = $classId;
        $this->section = $section;
        $this->studentIds = $studentIds;
        $this->termId = $termId;
        $this->report_batch = $report_batch;
        $this->action = $action;
    }

    public function handle() {

        $jobs = [];

        foreach (array_chunk($this->studentIds, 100) as $chunk) {
            foreach ($chunk as $studentId) {
                $jobs[] = new GenerateStudentReportJob(
                    $this->schoolId,
                    $this->classId,
                    $this->section,
                    (int) $studentId,
                    $this->termId,
                    $this->report_batch,
                    $this->action
                );
            }
        }

        $schoolId = $this->schoolId;
        $term_id = $this->termId;
        $classId = $this->classId;
        $section = $this->section;
        $report_batch_id =  $this->report_batch;
        $action =  $this->action;
        $studentCount = count($this->studentIds);

        if ($this->action === 'email') {
            Bus::batch($jobs)
                ->name('email_report_cards')
                ->onQueue('report_generation')
                ->dispatch();
        } else {
            Bus::batch($jobs)
                ->name("generate_report_card")
                ->then(function (Batch $batch) use ($schoolId, $term_id, $classId, $section, $report_batch_id, $studentCount) {               
                    MergeClassSectionPdfJob::dispatch($schoolId, $term_id, $classId, $section, $report_batch_id, $studentCount)->onQueue('report_merge');
                })
                ->catch(function (Batch $batch, Throwable $e) {
                    Log::error("Batch failed", [
                        'batch_id' => $batch->id,
                        'error' => $e->getMessage()
                    ]);
                })
                ->onQueue('report_generation')
                ->dispatch();

            // return response()->json(['batch_id' => $batch->id]);
        }
    }
}
