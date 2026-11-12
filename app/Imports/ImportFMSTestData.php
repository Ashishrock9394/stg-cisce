<?php

namespace App\Imports;

use DB;
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

class ImportFMSTestData implements ToCollection, WithHeadingRow, WithChunkReading
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
        
        $this->insertData = [];
        $this->imProperFormatData = [];

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

            $skillMapping = DB::table('skill_types')
                ->where('skill_report_id', $this->skillId)
                ->get()
                ->mapWithKeys(fn($item) => [$this->normalizeDescription($item->description) => $item->id]);
            $skillName = DB::table('skill_reports')->where('id', $this->skillId)->value('skill_name');

            foreach ($rows as $index => $row) {

                $originalRow = $row->toArray();
                $excelRow = $index + 4;

                $studentId = trim($row['student_id'] ?? '');
                $statusColumn = collect($row->keys())
                    ->first(fn($key) => str_contains(strtolower($key), 'status'));

                $status = trim($row[$statusColumn] ?? '');

                unset(
                    $row['student_id'],
                    $row['class'],
                    $row['section'],
                    $row['student_name'],
                    $row['roll_no'],
                    $row[$statusColumn]
                );

                $errors = [];
                $rowInsertData = [];

                if ($status === 'Not Attempted') {
                    continue;
                }
                if ($status === 'Attempted') {

                    $skillColumns = collect($row);

                    $allEmpty = $skillColumns->every(fn($value) => empty($value));

                    if ($allEmpty) {
                        $rowInsertData[] = [
                            'school_id' => $this->school_id,
                            'term_master_id' => $TermMasterId,
                            'student_id' => $studentId,
                            'skill_report_id' => $this->skillId,
                            'skill_type_id' => null,
                            'skill_type_value' => null,
                            'term_type_id' => 1,
                            'created_by' => $this->userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        
                        foreach ($skillColumns as $skillType => $score) {
                            $normalizedSkill = $this->normalizeDescription($skillType);
                            $skillTypeId = $skillMapping[$normalizedSkill] ?? null;

                            $score = strtoupper($score);

                            if ($score === null || $score === '') {
                                continue;
                            }

                            if ($score !== 'Y') {
                                $errors[] = "Invalid value in column '{$normalizedSkill}' at row {$excelRow}";
                                continue;
                            }

                            $rowInsertData[] = [
                                'school_id' => $this->school_id,
                                'term_master_id' => $TermMasterId,
                                'student_id' => $studentId,
                                'skill_report_id' => $this->skillId,
                                'skill_type_id' => $skillTypeId,
                                'skill_type_value' => $score,
                                'term_type_id' => 1,
                                'created_by' => $this->userId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
                if (!empty($errors)) {
                    $originalRow['Error'] = implode('; ', $errors);
                    $this->imProperFormatData[] = $originalRow;
                } else {
                    $this->insertData = array_merge($this->insertData, $rowInsertData);
                }
            }

            if (!empty($this->insertData)) {
                if ($this->action === 'override') {

                    $validStudentIds = array_column($this->insertData, 'student_id');
                    
                    DB::table('skillreport_skilltype_termtype_mapping')
                        ->where('school_id', $this->school_id)  
                        ->where('skill_report_id', $this->skillId)
                        ->where('term_master_id', $TermMasterId)
                        ->whereIn('student_id', $validStudentIds)
                        ->delete();
                }

                if ($this->action === 'skip') {
                    $existingStudentIds = DB::table('skillreport_skilltype_termtype_mapping')
                        ->where('school_id', $this->school_id)
                        ->where('skill_report_id', $this->skillId)
                        ->where('term_master_id', $TermMasterId)
                        ->pluck('student_id')
                        ->toArray();

                    $this->insertData = array_filter(
                        $this->insertData,
                        fn ($row) => !in_array($row['student_id'], $existingStudentIds)
                    );
                }

                if (!empty($this->insertData)) {
                    DB::table('skillreport_skilltype_termtype_mapping')
                        ->insert($this->insertData);
                }
                $studentYCount = [];

                foreach ($this->insertData as $row) {
                    $studentId = $row['student_id'];

                    if (!isset($studentYCount[$studentId])) {
                        $studentYCount[$studentId] = 0;
                    }

                    if ($row['skill_type_value'] === 'Y') {
                        $studentYCount[$studentId]++;
                    }
                }

                if (!empty($studentYCount)) {
                    foreach ($studentYCount as $studentId => $count) {
                        $score = $count . ' / 5';

                        $this->UpdateLowerTestStatus(
                            $studentId,
                            $TermMasterId,
                            $this->skillId,
                            $score,
                            $this->school_id,
                            null,
                            null
                        );
                    }
                }

            }


            $this->updateImportLog($skillName);

        } catch (\Throwable $e) {
            Log::error("FMS Import Error [school={$this->school_id}]: {$e->getMessage()}");
            throw $e;
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


    private function normalizeDescription($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);
        $text = preg_replace('/_+/', '_', $text);
        return trim($text, '_');
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
