<?php

namespace App\Imports;

use DB;
use Carbon\Carbon;
use App\Models\School;
use App\Models\Sstudent;
use App\Models\TermMaster;
use App\Models\TestImportLog;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\UpdateFitnessTestResults;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportFitnessTestData implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $school_id;
    protected $action;
    protected $userId;
    protected $skillId;
    protected $logId;

    protected $insertData = [];
    protected $imProperFormatData = [];
    protected $skippedCount = 0;
    use UpdateFitnessTestResults;

    public function __construct($schoolId, $action, $userId, $skillId, $logId)
    {
        $this->school_id = $schoolId;
        $this->action = $action;
        $this->userId = $userId;
        $this->skillId = $skillId;
        $this->logId = $logId;
    }

    public function headingRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        try {
            $studentIds = $rows->pluck('student_id')->filter()->map(fn($v) => trim((string)$v))->unique()->toArray();
            if (empty($studentIds)) return;

            $year = date('Y');
            $month = date('m');
            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

            $TermMasterId = TermMaster::where('school_id', $this->school_id)
                ->where('is_active', 1)
                ->where('academic_year', $academicYear)
                ->value('id');

            $skillDetails = DB::table('skill_reports')->where('id', $this->skillId)->first();
            
            foreach ($rows as $index => $row) {
                $originalRow = $row->toArray();
                $row = $originalRow;
                $excelRow = $index + 4;
                $studentId = trim($row['student_id'] ?? '');              
                $class = $row['class'] ?? null;
                $this->studentClasses[$studentId] = $class;

                unset($row['student_id'], $row['class'], $row['section'], $row['student_name'], $row['roll_no']);

                $data = [
                    'SchoolID'  => $this->school_id,
                    'StudentID'=> $studentId,
                    'TermId'   => $TermMasterId,
                    'TestTypeID'=> $this->skillId,
                    'CreatedBy'=> $this->userId,
                    'ModifiedBy'=> $this->userId,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ];
                $score = null;  

                // for BMI 
                if ($this->skillId == 18) {
                    $height = $row['height_cm_mm'] ?? null;
                    $weight = $row['weight_kg_gm'] ?? null;

                    if (
                        ($height === null || $height === '') &&
                        ($weight === null || $weight === '')
                    ) {
                        $this->skippedCount++;
                        continue;
                    }

                    if (
                        !is_numeric($height) || $height <= 0 ||
                        !is_numeric($weight) || $weight <= 0
                    ) {
                        $this->addError(
                            $originalRow,
                            'Height and Weight must be a positive number.',
                            $excelRow
                        );
                        continue;
                    }

                    $heightMeters = ($height) / 100;
                    $score = $weight / ($heightMeters ** 2);

                    $student = Sstudent::where('id', $studentId)->select('gender','dob')->first();
                    if (empty($student) || empty($student->dob)) {
                        $this->addError(
                            $originalRow,
                            'invalid student data: missing or invalid DOB.',
                            $excelRow
                        );
                        continue;
                    }

                    $dob = Carbon::parse($student->dob);
                    $studentAge = $dob->age;
                    $studentGender = $student->gender;
                    $ageGender = $studentAge . strtolower(substr($studentGender, 0, 1));

                    $result = DB::selectOne("CALL GetBmiLevel(?, ?)", [$ageGender, $score]);

                    if ($score <= 0 || is_infinite($score) || is_nan($score)) {
                        $this->addError(
                            $originalRow,
                            'invalid BMI score: '. $score,
                            $excelRow
                        );
                        continue;
                    }

                    $data['height'] = $height;
                    $data['weight'] = $weight;
                    $data['Score']  = $score;
                    $data['level']  = $result->level_code;
                }

                // For sit and reach
                elseif ($this->skillId == 22) {
                    $initial = $row['initial_position_cm_mm'] ?? null;
                    $final  =    $row['final_position_cm_mm'] ?? null;

                    if (
                        ($initial === null || $initial === '') &&
                        ($final === null || $final === '')
                    ) {
                        $this->skippedCount++;
                        continue;
                    }

                    if ($initial === null || $initial === '' || $final === null || $final === '') {
                        $this->addError(
                            $originalRow,
                            'Both initial and final positions are required.',
                            $excelRow
                        );
                        continue;
                    }

                    if (
                        !is_numeric($initial) || $initial < 0 ||
                        !is_numeric($final) || $final < 0
                    ) {
                        $this->addError(
                            $originalRow,
                            'Initial and final positions must be a positive number.',
                            $excelRow
                        );
                        continue;
                    }

                    $score = ((float)$final - (float)$initial) * 10;

                    if ($score < 0) {
                        $this->addError($originalRow, 'Initial position can\'t be grater than final position', $excelRow);
                        continue;
                    }

                    $levels = $this->GetFitnessTestLevel($studentId, $skillDetails->TestTypeMasterID, $score, 'More_is_better');

                    $data['InitialScore'] = (int)$initial;
                    $data['FinalScore']   = (int)$final;
                    $data['Score']        = $score;
                    $data['level']        = $levels;
                }

                // for plate tapping,  50m. and 600m. run/walk
                elseif (in_array($this->skillId, [16, 19, 20])) {

                    $min = $row['min'] ?? null;
                    $sec = $row['sec'] ?? null;
                    $ms  = $row['ms']  ?? null;

                    if (
                        ($min === null || $min === '') &&
                        ($sec === null || $sec === '') &&
                        ($ms  === null || $ms  === '')
                    ) {
                        $this->skippedCount++;
                        continue;
                    }
                    if (($min !== null && $min !== '' && (!is_numeric($min) || $min < 0)) ||
                        ($sec !== null && $sec !== '' && (!is_numeric($sec) || $sec < 0)) ||
                        ($ms  !== null && $ms  !== '' && (!is_numeric($ms) || $ms < 0))
                    ) {
                        $this->addError(
                            $originalRow,
                            'Minutes, seconds, and milliseconds must be non-negative integers.',
                            $excelRow
                        );
                        continue;
                    }
                    if($min > 59 || $sec >59 || $ms > 999){
                        $this->addError(
                            $originalRow,
                            'Minutes and seconds must be ≤ 59, and milliseconds must be ≤ 999.',
                            $excelRow
                        );
                        continue;
                    }

                    $min = (int)$min;
                    $sec = (int)$sec;
                    $ms  = (int)$ms;

                    $score = ($min * 60000) + ($sec * 1000) + $ms;
                    
                    if ($score <= 0) {
                        continue;
                    }
                        
                    $levels = $this->GetFitnessTestLevel($studentId, $skillDetails->TestTypeMasterID, $score, 'Less_is_better');
                    
                    $data['Score'] = $score;
                    $data['level'] = $levels;
                }

                // for flamingo partial curl up and push-up 
                elseif (in_array($this->skillId, [17, 21, 23])) {
                    $count = $row['count'] ?? null;

                    if (
                        ($count === null || $count === '')
                    ) {
                        $this->skippedCount++;
                        continue;
                    }

                    if (!ctype_digit((string)$count) || (int)$count < 0) {
                        $this->addError(
                            $originalRow,
                            'Please enter a whole number (0 or greater). Special characters, negative numbers, and decimals are not allowed.',
                            $excelRow
                        );
                        continue;
                    }

                    if($this->skillId == 17){
                        $levels = $this->GetFitnessTestLevel($studentId, $skillDetails->TestTypeMasterID, $count, 'Less_is_better');
                    }else{
                        $levels = $this->GetFitnessTestLevel($studentId, $skillDetails->TestTypeMasterID, $count, 'More_is_better');
                    }

                    $data['Score'] = $count;
                    $data['level'] = $levels;
                }

                if (!isset($data['Score']) || $data['Score'] < 0) {
                    continue;
                }

                $this->insertData[] = $data;

            }

            // echo"<pre>";print_r($this->insertData);exit();

            if (!empty($this->insertData)) {
                if ($this->action === 'override') {
                    $validStudentIds = array_column($this->insertData, 'StudentID');
                    DB::table('SeniorTestResults')
                        ->where('SchoolID', $this->school_id)
                        ->where('TestTypeID', $this->skillId)
                        ->where('TermId', $TermMasterId)
                        ->whereIn('StudentID', $validStudentIds)
                        ->delete();
                }
                
                if ($this->action === 'skip') {
                    $existingStudentIds = DB::table('SeniorTestResults')
                        ->where('SchoolID', $this->school_id)
                        ->where('TestTypeID', $this->skillId)
                        ->where('TermId', $TermMasterId)
                        ->pluck('StudentID')
                        ->toArray();

                    $this->insertData = array_values(array_filter(
                        $this->insertData,
                        fn ($row) => !in_array($row['StudentID'], $existingStudentIds)
                    ));
                }

                if (!empty($this->insertData)) {
                    DB::table('SeniorTestResults')->insert($this->insertData);

                    $studentScoreMap = [];
                    foreach ($this->insertData as $row) {
                        $studentScoreMap[$row['StudentID']] = [
                            'score'  => $row['Score'],
                            'height' => $row['height'] ?? null,
                            'weight' => $row['weight'] ?? null,
                            'class'  => $this->studentClasses[$row['StudentID']] ?? null,
                        ];
                    }
                    foreach ($studentScoreMap as $studentId => $data) {
                        $classNumber = (int) filter_var($data['class'], FILTER_SANITIZE_NUMBER_INT);

                        if (
                            $this->skillId == 16 ||
                            $this->skillId == 17 ||
                            ($this->skillId == 18 && $classNumber < 4)
                        ) {
                            $this->UpdateLowerTestStatus(
                                $studentId,
                                $TermMasterId,
                                $this->skillId,
                                $data['score'],
                                $this->school_id,
                                $data['height'],
                                $data['weight']
                            );
                        } else {
                            $this->UpdateSeniorTestStatus(
                                $studentId,
                                $TermMasterId,
                                $this->skillId,
                                $data['score'],
                                $this->school_id,
                                $data['height'],
                                $data['weight']
                            );
                        }
                    }

                    $this->insertData = [];
                    $this->studentClasses = [];
                }
            }

            $this->updateImportLog($skillDetails->skill_name);

        } catch (\Throwable $e) {
            Log::error("Fitness Import Error [school={$this->school_id}]: {$e->getMessage()}");
            throw $e;
        }
    }

    private function GetFitnessTestLevel($Student_id, $TestTypeMasterID, $Score, $ScoreCritera) {

		$student = Sstudent::where('id', $Student_id)->select('gender','dob','id')->first();
		$dob = Carbon::parse($student->dob);
	    $studentAge = $dob->age;
	    $gender = strtolower($student->gender) === 'male' ? 'Boys' : 'Girls';
	    
	    try {

		    $result = DB::selectOne("SELECT get_fitness_level(:skill_report_id, :age, :gender, :score, :criteria) AS level", [
	            'skill_report_id' => $TestTypeMasterID,
	            'age'             => $studentAge,
	            'gender'          => $gender,
	            'score'           => $Score,
	            'criteria'        => $ScoreCritera,
	        ]);

            return $result->level ?? 'N.A.';

         } catch (\Throwable $e) {

	    	\Log::error('Fitness Level Calculation Failed', [
		        'student_id' => $student->id,
		        'age' => $studentAge,
		        'exception' => $e->getMessage(),
		    ]);
		    return null;
	    }

	}

    protected function updateImportLog($skillName){

        $log = TestImportLog::find($this->logId);
        if (!$log) return;

        $schoolCode = School::find($this->school_id)?->school_code ?? 'school';
        $timestamp = $log->created_at?->format('YmdHis') ?? now()->format('YmdHis');

        $hasErrors = !empty($this->imProperFormatData);
        $hasSuccess = !empty($this->insertData);

        $file = null;

        if ($hasErrors) {
            $file = "test_errors/{$schoolCode}_{$timestamp}_errors.json";

            Storage::disk('azure')->put($file,
                json_encode($this->imProperFormatData, JSON_PRETTY_PRINT));

            // Storage::disk('local')->put(
            //     $file,
            //     json_encode($this->imProperFormatData, JSON_PRETTY_PRINT)
            // );
        }

        if ($hasErrors && $hasSuccess) {
            $status = 'completed_with_errors';
            $message = $skillName . ' data imported with some errors';
        } elseif ($hasErrors) {
            $status = 'failed';
            $message = $skillName . ' data import failed';
        } else {
            // Full success
            $status = 'completed';
            $message = $skillName . ' data imported successfully';
        }

        $log->update([
            'status' => $status,
            'message' => $message,
            'error_file' => $file,
            'completed_at' => now(),
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function addError(array $originalRow, string $message, int $excelRow)
    {
        $originalRow['Error'] = $message;
        $this->imProperFormatData[] = $originalRow;
    }


}

