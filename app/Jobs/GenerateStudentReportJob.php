<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\ReportHelperTrait;
use DB;
use App\Jobs\MergeClassSectionPdfJob;
use App\Jobs\SendStudentReportEmailJob;
use App\Models\EmailReportBatch;
use App\Models\EmailReportRequest;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class GenerateStudentReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable, ReportHelperTrait;

    public int $schoolId;
    public int $classId;
    public string $sectionId;
    public int $studentId;
    public int $termId;
    public string $action;
    public $timeout = 900;
    public $tries = 3;

    public $report_batch;

    public function __construct(int $schoolId, int $classId, string $sectionId, int $studentId, int $termId, $report_batch, $action)
    {
        $this->schoolId = $schoolId;
        $this->classId = $classId;
        $this->sectionId = $sectionId;
        $this->studentId = $studentId;
        $this->termId = $termId;
        $this->report_batch = $report_batch;
        $this->action = $action;
    }

    public function handle() {


        ini_set('memory_limit', '512M');
        set_time_limit(600);

        $relativePath = "{$this->schoolId}/batch_{$this->report_batch}/class_{$this->classId}/section_{$this->sectionId}";
        $disk = Storage::disk('reports');
        if (!$disk->exists($relativePath)) {
            $disk->makeDirectory($relativePath);
        }

        $tmpDir = $disk->path($relativePath);

        // if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);

        try {

            $pdfFile = $this->generateStudentPdfById($this->studentId, $this->termId, $tmpDir);

            if (!$pdfFile || !file_exists($pdfFile)) {
                throw new \Exception("PDF generation failed for student {$this->studentId}");
            }
    
            if ($this->action === 'email') {
                SendStudentReportEmailJob::dispatch(
                    $this->studentId,
                    $pdfFile,
                    $this->report_batch
                )->onQueue('import_emails');
            }

             \App\Models\ReportRequest::where('student_id', $this->studentId)
            ->update([
                'status' => 'generated',
                'file_path' => $relativePath . '/' . basename($pdfFile),
                'generated_at' => now(),
            ]);
            

        } catch (\Throwable $e) {

            if ($this->action === 'email') {
                EmailReportBatch::where('id', $this->report_batch)
                ->update([
                    'status' => 'failed',
                ]);
                Log::error("emailRequestJob failed", [
                    'student' => $this->studentId,
                    'err' => $e->getMessage(),
                    'batch_id' => $this->report_batch
                ]);
                throw $e;               

            }else{
                \App\Models\ReportBatch::where('id', $this->report_batch)
                ->update([
                    'status' => 'failed',
                ]);
                Log::error("GenerateStudentReportJob failed", [
                    'student' => $this->studentId,
                    'err' => $e->getMessage(),
                    'batch_id' => $this->report_batch
                ]);
                throw $e;
            }                       
        } 
    }
    

    protected function generateStudentPdfById($studentId, $termId, $batchDir){
 
        $studentsData = $this->getStudentData($studentId);

        $termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $termId);

        $currentTermId  = $termIds[0] ?? null;
        $previousTermId = $termIds[1] ?? null;

        $academicYear = DB::table('term_masters')->where('id', $currentTermId)->value('academic_year');
        
        $dob = \Carbon\Carbon::parse($studentsData->dob);
        $studentAge   = $dob->age;
        $studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
        $ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));
        // Fetch report + benchmarks
        $reportData = $this->getReportData($studentId, $termIds);

        if($studentsData->is_pwd == '1' && $studentsData->class_id >= 5){
            $pwd_cat_id = $studentsData->pwd_cat_id;
            $mappedReport  = $this->mapCWSNReportData($reportData, $studentAge, $studentGender, $ageGender);
			$groupedReport = $mappedReport->groupBy('Category')
			->map(function ($items) use ($currentTermId, $previousTermId) {
				return $items
					->filter(fn ($row) =>
						in_array((int) $row['TermId'], [$currentTermId, $previousTermId])
					)
					->groupBy(fn ($row) =>
						(int) $row['TermId'] === (int) $currentTermId
							? 'Current_Term'
							: 'Previous_Term'
					);
			});
			
			$getBmiBenchmark =  $this->getCWSNBmiBenchmark($studentAge, $studentGender);
			$orderedReportData = $this->getCWSNReportData($studentId, $pwd_cat_id, $studentAge, $studentGender, $groupedReport);
			$pdf = Pdf::loadView('reports.fitness.pdf.cwsn-pdf-report', compact('studentsData','orderedReportData', 'getBmiBenchmark','academicYear'));
        }else{
            $mappedReport  = $this->mapReportData($reportData, $studentAge, $studentGender, $ageGender);
            $groupedReport = $mappedReport->groupBy('Category')
            ->map(function ($items) use ($currentTermId, $previousTermId) {
                return $items
                    ->filter(fn ($row) =>
                        in_array((int) $row['TermId'], [$currentTermId, $previousTermId])
                    )
                    ->groupBy(fn ($row) =>
                        (int) $row['TermId'] === (int) $currentTermId
                            ? 'Current_Term'
                            : 'Previous_Term'
                    );
            });

            $getBmiBenchmark = $getBmiBenchmark =  $this->getBmiBenchmark($ageGender);

            
            if (in_array($studentsData->class_id, [4,5,6,7,8,9,10,11,12])) {				
                
                [$orderedReportData, $getFitnessBenchmark] = $this->getSeniorReportData($studentId, $studentAge, $studentGender, $groupedReport);
                            
                $pdf = Pdf::loadView('reports.fitness.pdf.senior-report', compact(
                    'studentsData','orderedReportData','getFitnessBenchmark','getBmiBenchmark','academicYear'
                ));

            } else {
                [$orderedReportData, $FmsReportData, $getFitnessBenchmark] = $this->getJuniorReportData($studentsData->class_id, $studentId, $studentAge, $studentGender, $groupedReport, $termIds);
            
                $pdf = Pdf::loadView('reports.fitness.pdf.junior-report', compact(
                    'studentsData','orderedReportData','FmsReportData','getFitnessBenchmark','getBmiBenchmark','academicYear'
                ));
            }
        }        

        $pdfFile = $batchDir . "/Fitness_Report_{$studentsData->student_name}.pdf";
        $pdf->save($pdfFile);
        return $pdfFile;
    }
}
