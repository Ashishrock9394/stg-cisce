<?php

namespace App\Imports;

use DB;
use Auth;
use DateTime;
use App\Models\School; 
use App\Models\Sclass;
use App\Models\Sstudent;
use App\Models\ScustomClass;
use App\Models\StudentImportLog;
use App\Exports\ExportImproperData;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

// use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Events\AfterImport;

class ImportStudentProfile implements ToCollection, WithHeadingRow , WithChunkReading  {

    protected $logId;
    protected $userId;
    protected $schoolId;
    protected $schoolCode;
    protected $action;
    protected $insertedRowIds = [];
    protected $importedData = [];
    protected $imProperFormatData = [];
    protected $skippedCount = 0;
    protected $insertData = [];

    // function __construct($schoolId,$action){
    function __construct($schoolId,$action,$userId,$logId) {
        $this->school_id = $schoolId;
        $this->action = $action;
        $this->userId = $userId;
        $this->logId = $logId;
        $this->schoolCode = School::where('id', $schoolId)->value('school_code');
    }

    public function getInsertedRowIds() { return $this->insertedRowIds; }
    public function getImportedData(){ return $this->importedData; }
    public function imProperFormatData(){ return $this->imProperFormatData;}
    public function getSkippedCount() { return $this->skippedCount;}


    public function collection(Collection $rows) : void {

        try{

            $insertData = [];
           
            $admissionNumbers = $rows->pluck('admissionnumber')->filter()->unique()->map(fn($val) => (string) trim($val))->toArray();
            if (empty($admissionNumbers)) {
                return;
            }
            $existingStudentsMap = Sstudent::where('school_id', (string)$this->school_id)
            ->where('academic_year', '2026-2027')
            ->whereIn('student_uid', $admissionNumbers)->pluck('id', 'student_uid');

            if ($this->action === 'override') { 
                Sstudent::where('school_id', (string)$this->school_id)
                ->where('academic_year', '2026-2027')
                ->whereIn('student_uid', $admissionNumbers)->delete();

                 $existingStudentsMap = [];
            }

            $alreadyInserted = []; 

            foreach ($rows as $key => $row) {
                $row = $row->toArray();
                $rowNumber = $key + 2;

                $admissionNo = trim($row['admissionnumber'] ?? '');

                if ($this->action === 'skipandimport') {
                    if ($existingStudentsMap->has($admissionNo) || isset($alreadyInserted[$admissionNo])) {
                        $this->skippedCount++;
                        continue;
                    }
                    // Track in current chunk
                    $alreadyInserted[$admissionNo] = true;
                    // $existingStudentsMap[$admissionNo] = true;
                }


                if (isset($existingStudentsMap[$admissionNo])) {

                    $this->skippedCount++;

                    $this->imProperFormatData[] = array_merge($row, [
                        'Error' => "Student already exists for academic year 2026-2027"
                    ]);

                    continue;
                }
                

                $gender = $this->normalizeGender($row['gender'] ?? '');
                $class = $this->convertRomanToNumeric(trim($row['class']), $row);
                $class_id = Sclass::whereRaw('LOWER(REPLACE(name, " ", "")) LIKE ?', ['%' .strtolower(str_replace(' ', '',trim($class))) .'%'])->value('id');
                if (!$class_id) {
                    $this->imProperFormatData[] = array_merge($row, ['Error' => 'Class not found']);
                    continue;
                }

                $section = strtoupper(trim($row['section'] ?? ''));
                $rollNo = trim($row['roll_no'] ?? '');
                $dob = trim($row['dob_ddmmyyyy'] ?? '');
                $pwdValue = trim($row['cwsn'] ?? '');
                if($pwdValue === 'YES'){
                    $is_pwd = '1';
                }else{
                    $is_pwd = '0';
                }


                try {
                    if (is_numeric($dob)) {

                        $dob = Date::excelToDateTimeObject($dob)->format('Y-m-d');
                    } else {
                        $formats = ['d/m/Y', 'd-m-Y'];

                        $parsedDate = null;
                        foreach ($formats as $format) {
                            try {
                                $parsedDate = \Carbon\Carbon::createFromFormat($format, $dob);
                                break; 
                            } catch (\Exception $e) {
                                // Try next format
                            }
                        }

                        if (!$parsedDate) {
                            throw new \Exception('Invalid date format');
                        }

                        $dob = $parsedDate->format('Y-m-d');
                    }
                } catch (\Exception $e) {

                    $this->imProperFormatData[] = array_merge($row, [
                        'Error' => "Invalid DOB format ({$row['dob_ddmmyyyy']}). Allowed formats: Excel date or dd/mm/yyyy"
                    ]);

                    continue;
                }

                $alreadyInserted[$admissionNo] = true;

                $customClassId = $this->findOrCreateCustomClass($class_id, $section);
                $name = trim($row['name'] ?? '');
                $firstName = explode(' ', $name);
                $data = [
                    'school_id'     => $this->school_id,
                    'school_code'   => trim($row['school_code'] ?? ''),
                    'student_uid'   => $admissionNo,
                    'student_name'  => $name,
                    'gender'        => $gender,
                    'class_id'      => $class_id,
                    'custom_class_id' => $customClassId,
                    'section_id'    => $section,
                    'dob'           => $dob,
                    'user_id'       => trim($row['school_code']) . $admissionNo,
                     'password'      => strtolower($firstName[0]) . '@' . $admissionNo,
                    'email_id'      => trim($row['email'] ?? ''),
                    'rollno'        => trim($row['roll_no'] ?? ''),
                    'domicile'      => trim($row['domicile_hometown'] ?? ''),
                    'fav_sport'     => trim($row['favorite_sports'] ?? ''),
                    'hobbies'       => trim($row['hobbies'] ?? ''),
                    'apaarId'       => trim($row['apaarid'] ?? ''),
                    'aadhaarId'     => trim($row['adhaarid'] ?? ''),
                    'passport'      => trim($row['passportnumber'] ?? ''),
                    'status'        => 'active',
                    'academic_year' => '2026-2027',
                    'is_pwd'        => $is_pwd,
                ];

                if ($this->action === 'override') {
                    $insertData[] = $data;           
                } elseif ($this->action === 'skipandimport') {
                    if (!$existingStudentsMap->has($admissionNo)) {
                        $insertData[] = $data;
                    }else {
                        $this->skippedCount++;
                    }
                } else {
                    $insertData[] = $data;
                }
            }

            if (!empty($insertData)) {
                $previousStudents = DB::table('students')
                    ->where('school_id', (string)$this->school_id)
                    ->where('academic_year', '2025-2026')
                    // ->where('status', 'active')
                    ->whereIn('status', ['active', 'failed', 'promoted'])
                    ->whereIn('student_uid', array_column($insertData, 'student_uid'))
                    ->get(['student_uid','class_id','section_id'])
                    ->keyBy('student_uid');

                $chunks = array_chunk($insertData, 100);

                foreach ($chunks as $chunk) {
                    Sstudent::insert($chunk);

                   /* foreach ($chunk as $student) {
                        $existingStudentsMap[$student['student_uid']] = true;
                    }
                    */
                    $promotedStudentUids = [];
                    $failedStudentUids = [];

                    foreach ($chunk as $student) {
                        $oldStudent = $previousStudents[$student['student_uid']] ?? null;                        
                        if (!$oldStudent) {
                            continue;
                        }

                        if ((int) $student['class_id'] > (int) $oldStudent->class_id) {
                            $promotedStudentUids[] = $student['student_uid'];

                        }elseif ((int) $student['class_id'] === (int) $oldStudent->class_id) {
    
                            $failedStudentUids[] = $student['student_uid'];
                        }
                    }

                    if (!empty($promotedStudentUids)) {

                        DB::table('students')
                        ->where('school_id', (string)$this->school_id)
                        ->where('academic_year', '2025-2026')
                        // ->where('status', 'active')
                         ->whereIn('status', ['active', 'failed', 'promoted'])
                        ->whereIn('student_uid', $promotedStudentUids)
                        ->update([
                            'status' => 'promoted',
                            'updated_at' => now(),
                        ]);
                    }

                    if (!empty($failedStudentUids)) {
                        DB::table('students')
                        ->where('school_id', (string)$this->school_id)
                        ->where('academic_year', '2025-2026')
                        ->whereIn('student_uid', $failedStudentUids)
                        ->update(['status' => 'failed', 'updated_at' => now()]);
                    }
                }

                $this->importedData = array_merge(
                    $this->importedData,
                    $insertData
                );
            }


           $this->updateImportLog();

        } catch (\Throwable $e) {


            $errorMessage = 'Import failed';
            $httpCode = 500;
            
            // Detect database lock errors
            if (str_contains($e->getMessage(), 'Lock wait timeout') || str_contains($e->getMessage(), 'deadlock') || str_contains($e->getMessage(), 'lock conflict')) {             
                $errorMessage = 'The system is currently busy processing other requests. Please try again in a few moments.';
                $httpCode = 423; 
            }

            \Log::error("Error in collection import for school_id {$this->school_id}: " . $e->getMessage());

            throw $e;
        } 

    }


    protected function updateImportLog() {

        $log = StudentImportLog::find($this->logId);
        $school_code = School::find($this->school_id)?->school_code;
        $timestamp = $log->created_at->format('YmdHis');
        $jsonFileName = 'import_errors/' . $school_code . '_' . $timestamp . '_errors.json';
        $jsonPath = storage_path("app/{$jsonFileName}");

        $directory = 'import_errors';
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        $existingErrors = [];
        if (file_exists($jsonPath)) {
            $existingErrors = json_decode(file_get_contents($jsonPath), true) ?? [];
        }

        $mergedErrors = array_merge($existingErrors, $this->imProperFormatData);

        if (!empty($mergedErrors)) {
            file_put_contents($jsonPath, json_encode($mergedErrors, JSON_PRETTY_PRINT));
        }


        $this->imProperFormatData = [];
        if (!empty($mergedErrors)) {
            StudentImportLog::where('id', $this->logId)->update([
                'error_file' => $jsonFileName,
                'status' => 'failed',
                'message' => 'Import completed with errors. Download error file.',
                'completed_at' => now(),
            ]);
        } else {
            StudentImportLog::where('id', $this->logId)->update([
                'status' => 'completed',
                'error_file' => '',
                'message' => 'Successfully imported students.',
                'completed_at' => now(),
            ]);
        }
    }



    protected function convertRomanToNumeric($roman) {

        $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = 0;
        $length = strlen($roman);

        if (!preg_match('/^[IVX]+$/', $roman)) {            
            if($roman == 'Pre-Nur'){
                $roman = 'Pre Nursery';
            }
            return $roman;
        }

        for ($i = 0; $i < $length; $i++) {
            $currentSymbol = $roman[$i];
            $nextSymbol = $i + 1 < $length ? $roman[$i + 1] : '';

            if (isset($map[$currentSymbol . $nextSymbol])) {
                $returnValue += $map[$currentSymbol . $nextSymbol];
                $i++; // Skip next symbol
            } else {
                $returnValue += $map[$currentSymbol];
            }
        }

        return 'Class ' .$returnValue;
    }


    protected function findOrCreateCustomClass($classId, $section) {

        $custom_class_id = DB::table('custom_classes')->where('class_id', $classId)->where('section', $section)->where('school_id', $this->school_id)->value('id');

        if (is_null($custom_class_id)) {

            $nomenclature = ScustomClass::where('class_id', $classId)->where('school_id', $this->school_id)->value('nomenclature');
            $maxOrder = ScustomClass::where('school_id', $this->school_id)->max('orders') ?? 0;
            $customClass = ScustomClass::create([
                'school_id' => $this->school_id,
                'class_id' => $classId,
                'section' => $section,
                'nomenclature' => $nomenclature ?? 'Class ' . $classId,
                'orders' => $maxOrder + 1,
                'status' => 1,
            ]);

            $custom_class_id = $customClass->id;
        }


        return $custom_class_id;
    }


    protected function normalizeGender($genderRaw) {

        $genderRaw = strtolower(trim($genderRaw));

        if (in_array($genderRaw, ['m', 'male','MALE'])) {
            return 'Male';
        } elseif (in_array($genderRaw, ['f', 'female','FEMALE'])) {
            return 'Female';
        }
        return null;
    }

    public function chunkSize(): int {
        return 300;
    }
}

