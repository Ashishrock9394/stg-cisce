<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TotProgram;
use App\Models\TotTrainerMapping;
use App\Models\User;
use App\Models\School;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainerTemplateExport;
use DB;
use App\Models\SchoolTrainer;
use Illuminate\Support\Facades\Log;
use App\Traits\TotHelpersTrait;

class TOTController extends Controller
{
    use TotHelpersTrait;

    protected $totSchoolId;
    protected $totSchoolCode;

    public function __construct() {
        
        $this->middleware(['auth', 'role:9']);
        $this->totSchoolId = 3131;
        $this->totSchoolCode = 'AK003';
    }

    /* Show all TOT programs */
    public function index() {

        $title = 'TOT Program';
        $totPrograms = TotProgram::select('id','name','location','start_date','end_date','status')->withCount(['trainerMappings as trainer_count'])
        ->get();
        return view('tot.index', compact('totPrograms','title'));
    }

    /* Show create TOT program form*/
    public function create() {

        $title = 'TOT program form ';
        return view('tot.create', compact('title'));
    }

    /* Store new TOT program */
    public function store(Request $request) {

        $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        TotProgram::create($request->all());
        return redirect()->route('tot.index')->with('success', 'TOT Program created successfully');
    }

    /* Manage all TOT data */
    public function manage($id)  {

        $program = TotProgram::findOrFail($id);
        $mappings = TotTrainerMapping::where('tot_program_id', $id)->with('trainer')->orderBy('created_at', 'desc')->get();

        $school_id = DB::table('students')->distinct()->pluck('school_id');
        $schools = DB::table('schools')->select('id','school_name','school_code')->whereIn('id', $school_id)->get();
                    
        //echo"<pre>"; print_r($schools);exit();

        $counts = [
            'students' => DB::table('students')
                ->where('school_code', $this->totSchoolCode)
                ->where('program_id', $id)
                ->count(),
                
            'trainers' => TotTrainerMapping::where('tot_program_id', $id)->count(),
            
            'classes' => DB::table('custom_classes')
                ->where('school_id', $this->totSchoolId)
                ->count(),
                
            'test_data' => DB::table('skillreport_skilltype_termtype_mapping')
                ->whereIn('student_id', function($query) use ($id) {
                    $query->select('id')
                        ->from('students')
                        ->where('school_code', $this->totSchoolCode)
                        ->where('program_id', $id);
                })
                ->count(),

            'senior' => DB::table('SeniorTestResults') 
            ->whereIn('StudentID', function($query) use ($id) {
                $query->select('id')->from('students')->where('SchoolID', $this->totSchoolId);
            })
            ->count(),
        ];

        // echo "<pre>"; print_r($counts);exit();
        $title = 'Manage TOT program';
        return view('tot.manage', compact('program', 'mappings','title','schools','counts'));
    }


    /* Copy Students Data To TOT Schools */
    public function copyStudents(Request $request, $programId)  {

        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $targetSchoolId = $request->school_id;
        $tot_school_id = $this->totSchoolId;
        $tot_school_code = $this->totSchoolCode;

        try{

            $classMappings = $this->mapClassesToTotSchool($targetSchoolId, $tot_school_id, $programId);

            DB::table('students')
            ->where('school_id', $targetSchoolId)
            ->where('status','active')
            ->orderBy('id')
            ->chunk(1000, function ($students) use ($programId,$tot_school_id, $tot_school_code, $classMappings) {
                $insertData = [];

                foreach ($students as $s) {

                    $mappedClass = $classMappings[$s->custom_class_id] ?? null;
                    if (!$mappedClass) {       
                        continue;
                    }

                    $insertData[] = [
                        'school_id'      => $tot_school_id,   //3131,     //ToT school ID
                        'school_code'    => $tot_school_code,  //'AK003',  // School code
                        'student_uid'    => $s->student_uid,
                        'student_name'   => $s->student_name,
                        'gender'         => $s->gender,
                        'class_id'       => $mappedClass['class_id'],
                        'custom_class_id'=> $mappedClass['custom_class_id'],
                        'section_id'     => $mappedClass['section'],
                        'dob'            => $s->dob,
                        'user_id'        => $s->user_id,
                        'password'       => $s->password,
                        'email_id'       => $s->email_id,
                        'rollno'         => $s->rollno,
                        'status'         => $s->status,
                        'remember_token' => $s->remember_token,
                        'domicile'       => $s->domicile,
                        'fav_sport'      => $s->fav_sport,
                        'hobbies'        => $s->hobbies,
                        'aadhaarId'      => $s->aadhaarId,
                        'apaarId'        => $s->apaarId,
                        'passport'       => $s->passport,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                        'program_id'     => $programId,
                    ];
                }


                // echo "<pre>"; print_r($insertData);exit();

                if (!empty($insertData)) {
                    DB::table('students')->insert($insertData);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Students copied successfully into TOT program.',
            ]);

         } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to copy students: ' . $e->getMessage(),
            ], 500);
        }
    }

    

    /* Get All TOT Students List */
    public function getStudents(Request $request, $programId) {
    
        $query = DB::table('students')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
            ->leftJoin('class', 'class.id', '=', 'students.class_id')
            ->select(
                'students.student_uid',
                'students.student_name as name',
                'students.gender',
                'students.dob',
                'students.class_id',
                'students.rollno',
                'students.section_id as section',
                'schools.school_name',
                DB::raw("
                    CASE 
                        WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                        THEN custom_classes.nomenclature 
                        ELSE class.name 
                    END AS classname
                ")
            )
            ->where('students.school_code', $this->totSchoolCode)
            ->where('students.program_id', $programId);

       
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('students.student_name', 'like', "%{$search}%")
                  ->orWhere('students.student_uid', 'like', "%{$search}%")
                  ->orWhere('schools.school_name', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $students = $query->offset($request->start ?? 0) ->limit($request->length ?? 10) ->get();
        // echo "<pre>"; print_r($students);exit();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $students,
        ]);
    }


    public function DownloadTrainerTemplate() {
        return Excel::download(new TrainerTemplateExport, 'TrainersPersonalProfile.xlsx');
    }


    /* Show upload page for trainers */
    public function uploadForm(TotProgram $totProgram) {

        $title = 'TOT program form ';
        return view('tot.upload', compact('totProgram','title'));
    }


    public function importTrainers(Request $request, TotProgram $totProgram) {
        \DB::beginTransaction();
        
        try {
            $request->validate([
                'trainer_file' => 'required|file|mimes:xlsx,xls|max:10240' // 10MB max
            ]);

            $file = $request->file('trainer_file');
            $totSchoolId = $this->totSchoolId;

            // Read Excel file
            $rows = Excel::toArray([], $file)[0] ?? [];
            
            if (count($rows) < 2) { // Header + at least one data row
                throw new \Exception('Excel file is empty or contains only headers.');
            }

            // Validate headers
            $expectedHeaders = ['Name', 'Email', 'Phone', 'Gender', 'DOB', 'Qualification', 'State', 'District', 'City', 'Address'];
            $actualHeaders = $rows[0] ?? [];
            
            $expected = array_map('strtolower', array_map('trim', $expectedHeaders));
            $actual = array_map('strtolower', array_map('trim', $actualHeaders));

            if ($expected !== $actual) {
                throw new \Exception('Invalid Excel template. Please download and use the official Trainer Template.');
            }

            $errors = [];
            $processed = 0;
            $skipped = 0;

            // Process rows (skip header)
            for ($index = 1; $index < count($rows); $index++) {
                $row = $rows[$index];
                
                try {
                    $name = trim($row[0] ?? '');
                    $email = strtolower(trim($row[1] ?? ''));
                    $mobile = trim($row[2] ?? '');
                    $gender = trim($row[3] ?? '');
                    $dob = trim($row[4] ?? '');
                    $qualification = trim($row[5] ?? '');
                    $state = trim($row[6] ?? '');
                    $district = trim($row[7] ?? '');
                    $city = trim($row[8] ?? '');
                    $address = trim($row[9] ?? '');

                    // Validate required fields
                    if (empty($email) || empty($name)) {
                        $errors[] = "Row " . ($index + 1) . ": Name and Email are required.";
                        $skipped++;
                        continue;
                    }

                    // Validate email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Row " . ($index + 1) . ": Invalid email format '{$email}'.";
                        $skipped++;
                        continue;
                    }

                    // Check for duplicate in TOT program
                    $user = DB::table('users')->where('email', $email)->first();
                    
                    if ($user) {
                        $existingTotMapping = TotTrainerMapping::where('tot_program_id', $totProgram->id)
                            ->where('trainer_id', $user->id)
                            ->first();

                        if ($existingTotMapping) {
                            $errors[] = "Row " . ($index + 1) . ": Trainer {$email} is already enrolled in this TOT program.";
                            $skipped++;
                            continue;
                        }
                    }

                    // Process trainer
                    if ($user) {
                        $this->processExistingTrainer(
                            $user, $name, $email, $mobile, $gender, $dob, 
                            $qualification, $state, $district, $city, $address,
                            $totProgram, $totSchoolId
                        );
                    } else {
                        $this->processNewTrainer(
                            $name, $email, $mobile, $gender, $dob,
                            $qualification, $state, $district, $city, $address,
                            $totProgram, $totSchoolId
                        );
                    }

                    $processed++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                    $skipped++;
                    \Log::error("Error processing row {$index} in TOT import", [
                        'error' => $e->getMessage(),
                        'row_data' => $row
                    ]);
                }
            }

            \DB::commit();

            // Prepare response
            $message = "Successfully processed {$processed} trainers.";
            if ($skipped > 0) {
                $message .= " {$skipped} rows skipped due to errors.";
            }

            return response()->json([
                'status' => empty($errors) ? 'success' : ($processed > 0 ? 'partial' : 'error'),
                'message' => $message,
                'processed' => $processed,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('TOT Import Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage(),
                'processed' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /* Get trainers list for DataTables  */
    public function getTrainers(Request $request, $programId) {
        $query = TotTrainerMapping::forProgram($programId)->with(['trainer', 'totSchool'])->select('tot_trainer_mappings.*');
        $totalCount = $query->count();

        // Search functionality
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('trainer', function($trainerQuery) use ($search) {
                    $trainerQuery->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Get filtered count
        $filteredCount = $query->count();

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');
        
        $orderableColumns = [
            1 => 'trainer.name', // Trainer Name
            2 => 'trainer.email', // Email
            3 => 'trainer.phone', // Phone
            4 => 'tot_trainer_mappings.status', // Status
            6 => 'tot_trainer_mappings.mapped_on' // Mapped On
        ];
        
        if (isset($orderableColumns[$orderColumn])) {
            if (str_contains($orderableColumns[$orderColumn], 'trainer.')) {
                $query->join('users as trainer', 'trainer.id', '=', 'tot_trainer_mappings.trainer_id')
                      ->orderBy($orderableColumns[$orderColumn], $orderDirection);
            } else {
                $query->orderBy($orderableColumns[$orderColumn], $orderDirection);
            }
        } else {
            $query->orderBy('tot_trainer_mappings.mapped_on', 'desc');
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        
        if ($length != -1) {
            $query->offset($start)->limit($length);
        }

        $mappings = $query->get();

        $data = $mappings->map(function($mapping, $index) use ($start) {
            $trainer = $mapping->trainer;
            
            return [
                'DT_RowIndex' => $start + $index + 1,
                'id' => $mapping->id,
                'trainer_name' => $trainer->name ?? 'N/A',
                'trainer_email' => $trainer->email ?? 'N/A',
                'trainer_phone' => $trainer->phone ?? 'N/A',
                'status' => $mapping->status,
                'original_schools_count' => $mapping->original_schools->count(),
                'mapped_on' => $mapping->mapped_on->format('d M Y'),
                'is_restored' => $mapping->isRestored(),
                'action' => $mapping->id // Placeholder for action buttons
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data
        ]);
    }

    /**
     * Get single trainer details (for modal)
     */
    public function getTrainerDetails($trainerMappingId) {

        $mapping = TotTrainerMapping::with(['trainer', 'trainer.usermeta'])->findOrFail($trainerMappingId);

        $trainer = $mapping->trainer;
        $usermeta = $trainer->usermeta ?? (object)[];

        return response()->json([
            'name' => $trainer->name,
            'email' => $trainer->email,
            'phone' => $trainer->phone,
            'gender' => $usermeta->gender ?? 'N/A',
            'dob'    => $usermeta->dob ? $usermeta->dob : 'N/A',
            'status' => $mapping->status,
            'mapped_on' => $mapping->mapped_on->format('d M Y H:i'),
            'original_schools_count' => $mapping->original_schools->count(),
            'original_schools' => $mapping->original_schools->map(function($school) {
                return [
                    'school_id' => $school['school_id'],
                    'original_status' => $school['original_status'],
                    'mapped_on' => \Carbon\Carbon::parse($school['mapped_on'])->format('d M Y H:i'),
                    'unmapped_on' => $school['unmapped_on'] ? \Carbon\Carbon::parse($school['unmapped_on'])->format('d M Y H:i') : null
                ];
            })->toArray()
        ]);
    }

    /**
     * Restore single trainer to their original schools
     */
    public function restoreSingleTrainer($trainerMappingId) {

        \DB::beginTransaction();
        
        try {
            $mapping = TotTrainerMapping::with('trainer')->notRestored()->findOrFail($trainerMappingId);
            $result = $this->restoreTrainerMapping($mapping);
            
            if ($result['success']) {
                \DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Trainer restored successfully to their original school(s)',
                    'trainer_name' => $mapping->trainer->name ?? 'Unknown'
                ]);
            }
            
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 500);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \DB::rollBack();
      
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found or already restored'
            ], 404);
            
        } catch (\Exception $e) {
            \DB::rollBack();
         
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore trainer: ' . $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * Bulk restore all trainers for a TOT program
     */
    public function restoreTrainers(Request $request, $totProgramId) {
        \DB::beginTransaction();
        
        try {
            // Find the TOT program
            $totProgram = TotProgram::findOrFail($totProgramId);
            
            $mappings = TotTrainerMapping::forProgram($totProgram->id)
                ->notRestored()
                ->with('trainer')
                ->get();

            $restoredCount = 0;
            $errors = [];

            foreach ($mappings as $mapping) {
                try {
                    $result = $this->restoreTrainerMapping($mapping);
                    
                    if ($result['success']) {
                        $restoredCount++;
                    } else {
                        $errors[] = "Trainer " . ($mapping->trainer->name ?? 'Unknown') . ": " . $result['error'];
                    }
                } catch (\Exception $e) {
                    $errors[] = "Trainer " . ($mapping->trainer->name ?? 'Unknown') . ": " . $e->getMessage();
                }
            }

            \DB::commit();

            // For AJAX requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully restored {$restoredCount} trainers." . 
                                (count($errors) > 0 ? " " . count($errors) . " errors occurred." : ""),
                    'restored_count' => $restoredCount,
                    'error_count' => count($errors),
                    'errors' => $errors
                ]);
            }

            // For regular requests, redirect with flash messages
            $message = "Successfully restored {$restoredCount} trainers.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return redirect()->back()->with(
                empty($errors) ? 'success' : 'warning',
                $message
            )->with('restore_errors', $errors);

        } catch (\Exception $e) {
            \DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk restoration failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Bulk restoration failed: ' . $e->getMessage());
        }
    }
}
