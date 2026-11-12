<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use App\Jobs\GenerateClassSectionReportJob;
use App\Jobs\MergeClassSectionPdfJob;
use App\Models\EmailReportBatch;

class GenerateSchoolReportsMasterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $schoolId;
    public array $studentIds;
    public int $termId;
    public int $report_batch;
    public string $action;

    public $timeout = 3600;
    public $tries = 3;

    public function __construct(int $schoolId, array $studentIds = [], $termId, $report_batch, $action) {
        $this->schoolId = $schoolId;
        $this->studentIds = $studentIds;
        $this->termId = $termId;
        $this->report_batch = $report_batch;
        $this->action = $action;
    }


    public function handle() {
        
        $students = DB::table('students')
            ->where('school_id', $this->schoolId)
            ->where('status', '!=', 'transfer')
            ->select('id', 'class_id', 'section_id')
            ->when(!empty($this->studentIds), fn($q) => $q->whereIn('id', $this->studentIds))
            ->get();


        if ($students->isEmpty()) {
            Log::warning("No students found", ['school_id' => $this->schoolId]);
            return;
        }

        $classSections = [];
        foreach ($students as $s) {
            $classSections[$s->class_id][$s->section_id][] = $s->id;
        }


        $totalStudents = $students->count();

        if ($this->action === 'email') {
            EmailReportBatch::where('id', $this->report_batch)
                ->update([
                    'total_students' => $totalStudents,
                    'status' => 'processing',
                ]);
        } else {
            \App\Models\ReportBatch::where('id', $this->report_batch)
                ->update([
                    'total_students' => $totalStudents,
                    'completed_students' => 0,
                    'status' => 'in_progress',
                ]);
        }
        
        foreach ($classSections as $classId => $sections) {
            foreach ($sections as $sectionId => $studentIds) {

                GenerateClassSectionReportJob::dispatch(
                    $this->schoolId,
                    $classId,
                    $sectionId,
                    $studentIds,
                    $this->termId,
                    $this->report_batch,
                    $this->action
                )->onQueue('report_generation');
            }
        }     

    }

}