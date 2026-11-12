<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\RpwdCategoryMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Traits\ReportHelperTrait;
use App\Models\SeniorTestResult;
use App\Traits\UpdateFitnessTestResults;
use Illuminate\Support\Facades\Auth;
use App\Models\TestTypeMaster;
use App\Models\Sstudent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TestResultAdditionalData;

class CwsnController extends Controller
{
    use ReportHelperTrait, UpdateFitnessTestResults;

    /**
     * Method to show all the cwsn Test category list.
     * Date : 09-07-2026
     * */

    public function showCWSNCategory($pwd_category_id,  $SeniorBMI = false) {

        $TestcategoryId = Crypt::decrypt($pwd_category_id);
        $CategoryName = DB::table('TestCategoryMaster')->where('TestCategoryID',$TestcategoryId)->value('TestCategoryName');
        
        $testType1 = DB::table('TestTypeMaster')->where('TestCategoryID',$TestcategoryId)
        ->where('TestsApplicable',5)
        ->orderBy('DisplayOrder')
        ->where('isActive', 1)
        ->get();


        $testType = DB::table('TestTypeMaster')
        ->join('pwd_category_test_mapping','pwd_category_test_mapping.TestTypeMasterID','=','TestTypeMaster.TestTypeID')
        ->join('pwd_categories','pwd_categories.id','=','pwd_category_test_mapping.pwd_category_id')
        ->where('TestTypeMaster.TestCategoryID',$TestcategoryId)
        ->where('TestTypeMaster.is_rpwd',1)

        ->select('TestTypeMaster.TestTypeID',
            'TestTypeMaster.TestCategoryID',
            'TestTypeMaster.DisplayOrder',
            'TestTypeMaster.TestsApplicable',
            'TestTypeMaster.TestTypeName',
            'TestTypeMaster.TestPerformed',
            'TestTypeMaster.is_rpwd',
            'pwd_category_test_mapping.pwd_category_id',
            'pwd_category_test_mapping.TestCategoryId',
            'pwd_categories.disability_category'
        )
        ->orderBy('TestTypeMaster.DisplayOrder')
        ->where('TestTypeMaster.isActive', 1)
        ->get();


        $testType = $testType->groupBy('TestTypeID')->map(function ($items) {
            $firstItem = $items->first();
            $firstItem->disability_categories = $items->pluck('disability_category')->toArray();
            return $firstItem;
        })->values();


        $title = $CategoryName ?? 'Test';
        $testTypeIds = $testType->pluck('TestTypeID');

        $videos = DB::table('fitness_test_videos')->whereIn('testType_id', $testTypeIds)->get();
        return view('assessor.cwsnSkillsTest', compact('title','testType', 'SeniorBMI','videos','pwd_category_id'));       
    }

    /**
     * Date : 24-08-2026
     * Method to get all skill test for CWSN mapped with the available cwsn.
     * Note : This method is not in working.
     * */
    public function CWSNSkillsTest($pwd_category_id, $test_category_id,  $SeniorBMI = false) {

        $CategoryName = DB::table('TestCategoryMaster')->where('TestCategoryID',$test_category_id)->value('TestCategoryName');
        $testType = RpwdCategoryMapping::where('pwd_category_id', $pwd_category_id)
            ->where('TestCategoryId', $test_category_id)
            ->with('testType')
            ->get()
            ->pluck('testType')->sortBy('TestTypeName');

        $title = $CategoryName ?? 'Test';
        $testTypeIds = $testType->pluck('TestTypeID');

        $videos = DB::table('fitness_test_videos')
            ->whereIn('testType_id', $testTypeIds)
            ->get();
        return view('assessor.cwsnSkillsTest', compact('title','testType', 'SeniorBMI','videos','pwd_category_id'));        
    }

    /**
     * UI Selection for CWSN Test
     * */
    public function CwsnTestTypes($pwd_category_id, $TestTypeId, $SeniorBMI = false) {

        $pwd_category_id = Crypt::decrypt($pwd_category_id);

        $skillReport = DB::table('skill_reports')->select('id','skill_name','TestTypeMasterID')->where('TestTypeMasterID',$TestTypeId)->first();
        $skillReportId     = $skillReport->id;
        $TestTypeMasterID  = $TestTypeId;
        $userId  = \Auth::id();


        if(Session::get('SelectSchoolId')) {    
            $SchoolId = Session::get('SelectSchoolId');
        } else {
            $SchoolTrainers = DB::table('school_trainers')
            ->join('schools','schools.id','=','school_trainers.school_id')
            ->select('schools.school_name','schools.id','schools.logo')
            ->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->get();

            if ($SchoolTrainers->isEmpty()) {               
                return redirect()->route('filldart.dashboard');           
            } else {
                $SchoolId = $SchoolTrainers->first()->id;
            }           
        }
        
        $classes = DB::table('custom_classes')
        ->join('class','class.id','=','custom_classes.class_id')
        ->select('custom_classes.id','class_id','section', DB::raw("CASE 
                WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                THEN custom_classes.nomenclature 
                ELSE class.name 
            END AS classname")
        )
        ->whereIn('class.id', array(5,6,7,8,9,10,11,12))
        ->Where('school_id', $SchoolId)
		->orderBy('class.id', 'ASC')
		->orderBy('custom_classes.section', 'ASC')
        ->get();

        
        $skillTypes = DB::table('skill_types')->where('skill_report_id',$skillReport->id)->where('status', 1)->get();
        $title = $skillReport->skill_name;



        // echo "<pre>"; print_r($pwd_category_id);exit();

        switch ($skillReport->skill_name) {

            case '20-m PACER':
            case 'Modified 15-m PACER':
                return view('assessor.cwsn.forms.pacer', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','pwd_category_id','TestTypeId'));
                break;

            case 'One-mile run/walk':
                return view('assessor.cwsn.forms.speed', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','pwd_category_id','TestTypeId'));
                break;

            case 'Curl-up':
            case 'Modified Curl-up':
                return view('assessor.cwsn.forms.curlup', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));

            case 'BMI':

                return view('assessor.cwsn.forms.cwsn-bmi', compact('title', 'skillTypes','skillReportId','TestTypeMasterID', 'classes', 'SchoolId','pwd_category_id','TestTypeId'));
                break;

            case 'Push-up':   //start button with beep at 3sec of intervwal with live count tracker
            case 'Dumbbell Press':  // start button with cadence at 4sec of intervwal with live count tracker (50 counts)
            case 'Pull-up':         // without timer
            case 'Modified Pull-Up': // withput timer
                return view('assessor.cwsn.forms.pushup', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case 'Trunk Lift':                
                return view('assessor.cwsn.forms.trunk-lift', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;
            
            case 'Back Saver Sit and Reach':
                return view('assessor.cwsn.forms.sit-and-reach', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case 'Reverse curl':
                return view('assessor.cwsn.forms.reverse_curl', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case 'Seated Push-up':
            case 'Isometric Push-up':
                return view('assessor.cwsn.forms.seated-pushup', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case 'Shoulder Stretch':
                return view('assessor.cwsn.forms.shoulder-streatch', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case 'Modified Apley Test':                
                return view('assessor.cwsn.forms.apleytest', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            case '40-Meter Push/Walk Test':                
                return view('assessor.cwsn.forms.push-walk', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','TestTypeId','pwd_category_id'));
                break;

            default:

                echo "default route"; exit;
                return view('assessor.cwsn.forms.pacer', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId','pwd_category_id','TestTypeId','pwd_category_id'));
                break;
        }
    }


    /**
     * CWSN Form Submission.
     * */
    public function SubmitCwsnTest(Request $request) {

        $skillReportId = (int) $request->input('skillReportId');
         $this->validateCwsnTestRequest($request, $skillReportId);
       
        $alldata = $request->all();
        $userId  = \Auth::id();
        
        if(Session::get('SelectSchoolId'))  {   
            $SchoolId = Session::get('SelectSchoolId');            
        }else {            
            $SchoolTrainers = DB::table('school_trainers')
            ->join('schools','schools.id','=','school_trainers.school_id')
            ->select('schools.school_name','schools.id','schools.logo')
            ->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->get();

            if ($SchoolTrainers->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No active school assigned to trainer.']);
            }
            $SchoolId = $SchoolTrainers[0]->id;           
        }

        $TermMasterId =  $this->getTermId($SchoolId);      
        // echo "<pre>"; print_r($skillReportId); exit();
        switch ($skillReportId) {

            case 29:        //20-m PACER
            case 30:        //Modified 15-m PACER
                
                if($alldata['laps_completed'] !='' && $alldata['student_id'] !='')  {

                    $totalLaps = (int) $request->input('laps_completed');
                    $TestScore = $totalLaps;
                    $levels = 'N.A.';
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);
                    
                } 
                return response()->json(['success' => false, 'message' => 'Laps calculation data missing.']);
                break;

            
            case 32:        //One-mile run/walk               
                $students = $request->students ?? [];

                if (!empty($students)) {
                    $studentIdsArray = [];

                    foreach($students as $key => $val) {
                        $studentId = $val['id'];
                        $studentTime =$val['time']; 

                        $studentIdsArray[] = $studentId; 
                        $alldata['student_id'] = $studentId;

                        $oneMileResult = $this->calculateOneMileResult($studentId, $SchoolId, $TermMasterId, $studentTime);

                        if (!$oneMileResult['success']) {
                            return response()->json([
                                'success' => false,
                                'message' => $oneMileResult['message']
                            ]);
                        }

                       //echo "<pre>"; print_r($oneMileResult); exit();

                        $TestScore = $oneMileResult['vo2_max']; 
                        $levels = 'N.A.';

                        $studentTime = [
                            'TimeTaken' => $studentTime,
                        ];

                        $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels, $studentTime);
                    }

                    $message =  $this->TestMessage($studentIdsArray, $skillReportId);
                    return response()->json(['success' => true,'message' => $message]); 
                }

                return response()->json(['success' => false, 'message' => 'No student performance dataset selected.']);
                break;
            case 43:        //Curl Up
            case 44:        //Modified Curl Up               
                if (isset($alldata['count_total_number']) && $alldata['count_total_number'] !== '' && !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $TestScore = $alldata['count_total_number'];
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);      
                    
                } 
                return response()->json(['success' => false, 'message' => 'Repetition summary missing.']);
                break;
            case 46:        // Pull-up
            case 47:        // Push-up
            case 57:        // Modified Pull-Up
            case 45:        // Dumbbell Press
                if (isset($alldata['total_push_up']) && $alldata['total_push_up'] !== '' && !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $TestScore = $alldata['total_push_up'];
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 
                return response()->json(['success' => false, 'message' => 'Total score value target missing.']);
                break;
            case 49:        // Trunk Lift

                if (isset($alldata['trunk_lift']) && $alldata['trunk_lift'] !== '' && !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $TestScore = $alldata['trunk_lift'];
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 
                return response()->json(['success' => false, 'message' => 'Trunk lift metrics missing.']);
                break;
            case 48:        //Seated Push-Up
            case 55:        // Isometric Push-Up
                 if (isset($alldata['modified_pushup']) && $alldata['modified_pushup'] !== '' && !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $modified_pushup = $alldata['modified_pushup'];
                    $TestScore = $this->timeToMilliseconds($modified_pushup);
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 
                return response()->json(['success' => false, 'message' => 'Duration measurements missing.']);
      
                break;
            case 56:        // Reverse Curl-up

                if (isset($alldata['reverse_curlup']) && $alldata['reverse_curlup'] !== '' && !empty($alldata['student_id'])) {               
                    $levels = 'N.A.';
                    $TestScore = $alldata['reverse_curlup'];
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 
                return response()->json(['success' => false, 'message' => 'Pass/Fail assessment result missing.']);
                break;
            case 37:        // Modified Apley Test

                if (isset($alldata['aplay_test']) && $alldata['aplay_test'] !== '' && !empty($alldata['student_id'])) {               
                    $levels = 'N.A.';
                    $alldata['RightScore'] =  $alldata['right_apley_level'];
                    $alldata['LeftScore'] =  $alldata['left_apley_level'];
                    $alldata['additional_score'] =  $alldata['LeftScore'] . ' | ' . $alldata['RightScore'];
                    $TestScore = null;
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 

                return response()->json(['success' => false, 'message' => 'Right Aplay /Left Aplay assessment result missing.']);
                break;
            case 35:        //Shoulder Streatch Test

                if (isset($alldata['score_left']) && $alldata['score_left'] !== '' && 
                isset($alldata['score_right']) && $alldata['score_right'] !== '' && 
                !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $alldata['RightScore'] =  $alldata['right_shoulder_status'];
                    $alldata['LeftScore'] =  $alldata['left_shoulder_status'];
                    $alldata['additional_score'] =($alldata['LeftScore'] == 1 ? 'P' : 'F') . ' | ' . ($alldata['RightScore'] == 1 ? 'P' : 'F');
                    $TestScore = null;

                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);                    
                } 

                return response()->json([
                    'success' => false, 
                    'message' => 'Both Left and Right arm assessment metrics are required.'
                ]);
                break;
            case 36:        // Back Saver Sit and Reach

                if (isset($alldata['score_left']) && $alldata['score_left'] !== '' && 
                    isset($alldata['score_right']) && $alldata['score_right'] !== '' && 
                    !empty($alldata['student_id'])) {

                    $levels = 'N.A.';
                    $alldata['RightScore'] =  (float) $alldata['score_right'] * 10; 
                    $alldata['LeftScore'] =  (float) $alldata['score_left'] * 10;
                    $alldata['additional_score'] =  $alldata['LeftScore']/10 . ' | ' . $alldata['RightScore']/10;
                    $TestScore = null;
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels); 
                }
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Both valid Left and Right leg performance dimensions are required.'
                ]);
                break;

            case 58:


                if (isset($alldata['assessment_result']) && $alldata['assessment_result'] !== '' && 
                    isset($alldata['heart_rate_status']) && $alldata['heart_rate_status'] !== '' && 
                    !empty($alldata['student_id'])) {

                    $additionalData = [
                        'TimeTaken' => round(((float) $request->input('timeTaken')) / 1000, 2),
                        'MobilityType' => match ($request->input('mobility_mode')) {
                            'legs' => 1,
                            'arms' => 2,
                            default => null,
                        },
                        'PulseCount' => $request->input('pulse_rate')
                            ? (int) $request->input('pulse_rate')
                            : null,
                    ];

                    //echo "<pre>"; print_r($additionalData); exit();

                    $levels = 'N.A.';
                    $assessment_result =  $alldata['assessment_result'];
                    $heart_rate_status =  $alldata['heart_rate_status'];
                    $TestScore = false;

                    if($assessment_result == 1 && $heart_rate_status){
                        $TestScore = true;
                    }
                    
                    return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels, $additionalData);                    
                } 

                return response()->json(['success' => false, 'message' => 'Right Aplay /Left Aplay assessment result missing.']);
            case 33:        // Cswn BMI Calculation
                 
                $BmiData  = $this->CwsnBmiCalcualtion($alldata);
                if(empty($BmiData))  return 'BMI is missing';

                $alldata['weight'] = $BmiData['weight'];
                $alldata['height'] = $BmiData['height'];

                $studentId = $alldata['student_id'];
                $schoolId = $alldata['SchoolId'];

                $run_walk_id = 32;
                $oneMileScore = DB::table('SeniorTestResults')->where('StudentID', $studentId)
                ->where('SchoolID', $schoolId)->where('TermId', $TermMasterId)
                ->where('TestTypeID', $run_walk_id)->orderBy('ResultId', 'desc')
                ->first();

                if( $oneMileScore && (int) $oneMileScore->Score === 1001){

                    $timeInMillisec = $oneMileScore->InitialScore; 
                    $bmi = $BmiData['bmiScore'];
                    $oneMileResult = $this->calculateOneMileResult($studentId, $schoolId, $TermMasterId, $timeInMillisec, $bmi);

                    if (!empty($oneMileResult['success'])) {
                        $vo2Max = $oneMileResult['vo2_max'];
                        DB::table('SeniorTestResults')->where('ResultId', $oneMileScore->ResultId)
                        ->update([
                            'Score' => $vo2Max,
                            'updated_at' => now()
                        ]);

                        $this->UpdateCWSNTestStatus($studentId, $TermMasterId, $run_walk_id , $vo2Max ,$schoolId , $alldata['height'], $alldata['weight']);
                    }
                }

                $TestScore = $BmiData['bmiScore'];
                $levels = 'N.A.';
                return $this->storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels);
    

                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid Test Type assignment mapping error.']);
                break;
        }
    }


    private function validateCwsnTestRequest(Request $request, int $skillReportId) {

        $rules = [
            'skillReportId' => 'required|integer',
            'SchoolId'      => 'required|exists:schools,id',
        ];

        if ($skillReportId !== 32) {
            // $rules['student_id'] = 'required|integer|min:1';
            $rules['student_id'] = 'required|exists:students,id';
        }

        switch ($skillReportId) {
            case 29:
            case 30:
                $rules['laps_completed'] = 'required|integer|min:0';
                break;

            case 32:
                $rules['students'] = 'required|array|min:1|max:500';
                $rules['students.*'] = 'required|array';
                $rules['students.*.id'] = 'required|integer|min:1';
                $rules['students.*.time'] = 'required|numeric|gt:0';
                break;

            case 43:
            case 44:
                $rules['count_total_number'] = 'required|integer|min:0|max:75';
                break;

            case 45:
            case 46:
            case 47:
            case 57:
                $rules['total_push_up'] = 'required|integer|max:999';
                break;

            case 49:
                // $rules['trunk_lift'] = 'required|numeric|min:0';
                $rules['trunk_lift'] = 'required|numeric|min:0|max:300';
                break;

            case 48:
            case 55:
                $rules['modified_pushup'] = 'required|string|max:20';
                break;

            case 56:
                // $rules['reverse_curlup'] = 'required|integer|min:0';
                $rules['reverse_curlup'] = 'required|integer|min:0|max:100';
                break;

            case 37:
                $rules['right_apley_level'] = 'required|integer';
                $rules['left_apley_level'] = 'required|integer';
                break;

            case 35:
                $rules['right_shoulder_status'] = 'required|integer|in:0,1';
                $rules['left_shoulder_status'] = 'required|integer|in:0,1';
                break;

             case 36:
                $rules['score_right'] = 'required|numeric|min:0|max:100';
                $rules['score_left'] = 'required|numeric|min:0|max:100';
                break;

            case 58:
                $rules['assessment_result'] = 'required|integer|in:0,1';
                $rules['heart_rate_status'] = 'required|integer|in:0,1';
                $rules['timeTaken'] = 'nullable|numeric|min:0';
                $rules['mobility_mode'] = 'nullable|string|in:legs,arms';
                $rules['pulse_rate'] = 'nullable|integer|min:30|max:250';
                break;

            // case 33:
            //     $rules['height']                  = ['nullable', 'numeric', 'min:30', 'max:250'];
            //     $rules['segment_floor_to_knee']   = ['nullable', 'numeric', 'min:10', 'max:150'];
            //     $rules['segment_knee_to_hip']     = ['nullable', 'numeric', 'min:10', 'max:150'];
            //     $rules['segment_hip_to_head']     = ['nullable', 'numeric', 'min:20', 'max:200'];
            //     $rules['amputation_raw_weight']   = ['nullable', 'numeric', 'min:2', 'max:300'];
            //     $rules['anthropo_wt_id']          = ['nullable', 'integer'];
            //     $rules['total_combined_weight']   = ['nullable', 'numeric', 'min:5', 'max:500'];
            //     $rules['wheelchair_tare_weight']  = ['nullable', 'numeric', 'min:0', 'max:200'];
            //     $rules['weight']                  = ['nullable', 'numeric', 'min:2', 'max:300'];
            //     break;

            case 33:
                $rules['height'] = [ 'nullable',   'numeric', 'gt:0' ];
                $rules['segment_floor_to_knee'] = [ 'nullable','numeric','gt:0'  ];
                $rules['segment_knee_to_hip'] = ['nullable',  'numeric', 'gt:0' ];
                $rules['segment_hip_to_head'] = [ 'nullable','numeric', 'gt:0'];
                $rules['amputation_raw_weight'] = ['nullable', 'numeric', 'gt:0'];
                $rules['anthropo_wt_id'] = [ 'nullable', 'integer'];
                $rules['total_combined_weight'] = ['nullable', 'numeric', 'gt:0'];
                $rules['wheelchair_tare_weight'] = [  'nullable',  'numeric','gte:0' ];
                $rules['weight'] = ['nullable', 'numeric',  'gt:0' ];
                break;

            default:
                abort(422, 'Invalid test type.');
        }

        $messages = [
            'students.*.time.min' => 'The time recorded for student rows must be at least 1 second (1000 ms).',
            'students.*.id.exists' => 'One or more selected students do not exist in database.',
            'total_push_up.required' => 'Please enter Push-up Counts',
            'total_push_up.integer'  => 'Push-up Counts Should be integer type',
            'total_push_up.max'     => 'Maximum count for Push-up reached',

            'trunk_lift.required'   => 'Please enter at least one trial measurement position.',
            'trunk_lift.numeric'    => 'please enter integer value',
            'trunk_lift.max'        => 'you can not lift the trunk more than 30 cm.',

        ];


        return $request->validate($rules, $messages);
    }

    protected function CwsnBmiCalcualtion($alldata){

        $height = 0; $weight = 0;

        if (!empty($alldata['height']) && is_numeric($alldata['height'])) {
            $height = (float) $alldata['height'];
        } elseif (isset($alldata['segment_floor_to_knee'], $alldata['segment_knee_to_hip'], $alldata['segment_hip_to_head']) ) {
            $height = (float) ($alldata['segment_floor_to_knee'] + $alldata['segment_knee_to_hip'] + $alldata['segment_hip_to_head']);
        }

        if(isset($alldata['amputation_raw_weight'] , $alldata['anthropo_wt_id'])){
           
            $rawWeight =  (float) $alldata['amputation_raw_weight'];

            switch ($alldata['anthropo_wt_id']) {
                case 5:

                    $weight = $rawWeight + ($rawWeight/18);     //below knee amputaion
                    break;

                case 6:
                    $weight = $rawWeight + ($rawWeight/9);      //Above knee amputaion 
                    break;

                case 7:
                    $weight = $rawWeight + ($rawWeight/6);     // Hip amputation
                    break;

                default:
                    $weight = $rawWeight;
                    break;
            }

        } elseif (isset($alldata['total_combined_weight'], $alldata['wheelchair_tare_weight'])) {

            $weight = (float)($alldata['total_combined_weight'] - $alldata['wheelchair_tare_weight']);

        } elseif (!empty($alldata['weight']) && is_numeric($alldata['weight'])) {

            $weight = (float)$alldata['weight'];
        }

        if ($height <= 0) {
            return 0; 
        }

        $heightInMeters = $height / 100;
        $bmiScore = $weight / ($heightInMeters * $heightInMeters);
        $score = round($bmiScore, 2);
        $bmiData = ['bmiScore' => $score, 'height' => $height, 'weight' => $weight];
        return $bmiData;
    }

    protected function storeFormData($alldata, $TermMasterId, $userId, $TestScore, $levels, $additionalData = null){

        $height = $alldata['height'] ?? null;
        $weight = $alldata['weight'] ?? null;

        $Result = new SeniorTestResult();
        $Result->SchoolID     = $alldata['SchoolId'];
        $Result->StudentID    = $alldata['student_id'];
        $Result->TermId       = $TermMasterId;
        $Result->TestTypeID   = $alldata['skillReportId'];
        $Result->Score        = $TestScore;
        if ($alldata['skillReportId'] == 32) {   
            $Result->InitialScore = $additionalData['TimeTaken'];
        }
        $Result->height       = $height;
        $Result->weight       = $weight;
        $Result->LeftScore    = $alldata['LeftScore'] ?? null;
        $Result->RightScore   = $alldata['RightScore'] ?? null;

        $Result->created_at   = now();
        $Result->CreatedBy    = $userId;
        $Result->updated_at   = now();
        $Result->ModifiedBy   = $userId;
        $Result->level        = $levels;

        $Result->save();

        if (!empty($additionalData) && $alldata['skillReportId'] == 58 || $alldata['skillReportId'] == 32 ) {

            $additionalResult = new TestResultAdditionalData();

            $additionalResult->ResultId = $Result->ResultId;
            $additionalResult->StudentId = $Result->StudentID;
            $additionalResult->TestTypeId = $Result->TestTypeID;
            $additionalResult->TimeTaken = $additionalData['TimeTaken'] ?? null;
            $additionalResult->MobilityType = $additionalData['MobilityType'] ?? null;
            $additionalResult->PulseCount = $additionalData['PulseCount'] ?? null;
            $additionalResult->AdditionalData = $additionalData['AdditionalData'] ?? null;
            
            $additionalResult->save();
        }
        
        
        if (in_array($alldata['skillReportId'], [35, 36, 37])){
            $this->UpdateCWSNTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'] , $alldata['additional_score'] ,$alldata['SchoolId'] , $height, $weight);
        }else{
            $this->UpdateCWSNTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'] , $TestScore ,$alldata['SchoolId'] , $height, $weight);
        }

        $message = $this->TestMessage($alldata['student_id'], $alldata['skillReportId']);

        return response()->json(['success' => true,'message' => $message]);    
    }
  

    private function TestMessage($studentIds, $testTypeId) {

        $testNames = [
            29  => '20-m PACER',
            30  => 'Modified 15-m PACER',
            26  => 'Target Aerobic Movement Test',
            32  => 'One-mile run/walk',
            33  => 'BMI',
            35  => 'Shoulder Stretch',
            36  => 'Back Saver Sit and Reach',
            37  => 'Modified Apley Test',
            38 => 'Modified Thomas Test',
            39 => 'Target Stretch Test',
            43 => 'Curl-up',
            44 => 'Modified Curl-up',
            45 => 'Dumbbell Press',
            46 => 'Pull-up',
            47 => 'Push-up',
            48 => 'Seated Push-up',
            49 => 'Trunk Lift',
            50 => 'Wheelchair Ramp Test',
            51 => 'Bench Press',
            52 => 'Flexed Arm Hang',
            53 => 'Extended Arm Hang',
            54 => 'Dominant Grip Strength',
            55 => 'Isometric Push-up',
            56 => 'Reverse curl',
            57 => 'Modified Pull-Up',
            58 => '40-Meter Push/Walk Test'
        ];

        if (!isset($testNames[$testTypeId])) {
            return "Unknown test (ID: {$testTypeId}).";
        }

        $studentIds = is_array($studentIds) ? $studentIds : [$studentIds];
        $studentNames = DB::table('students')->whereIn('id', $studentIds)->pluck('student_name')->toArray();

        if (empty($studentNames)) {
            return "No student found for this test.";
        }

        if (count($studentNames) === 1) {
            $namesStr = $studentNames[0];
        } else {
            $last = array_pop($studentNames);
            $namesStr = implode(', ', $studentNames) . ' and ' . $last;
        }

        return "The {$testNames[$testTypeId]} score for {$namesStr} has been saved in the database.";
    }

    private function UpdateCWSNTestStatus($studentId, $termId, $testTypeId, $score, $schoolId, $height, $weight) {


        $columns = [
            29 => '20m_pacer',
            30 => '15m_pacer',
            32 => '1mile_run_walk',
            35 => 'shoulder_stretch',
            36 => 'sit_and_reach',
            37 => 'modified_apley_test',
            43 => 'curlup',
            44 => 'modified_curlup',
            45 => 'dumbbell_press',
            46 => 'pullup',
            47 => 'pushup',
            48 => 'seated_pushup',
            49 => 'trunk_lift',
            55 => 'Isometric_pushup',
            56 => 'reverse_curl',
            57 => 'modified_pullup',
            58 => '40m_push_walk',
            33 => 'cwsn_bmi'        
        ];

        if (!isset($columns[$testTypeId])) {
            return; // invalid test type
        }

        $column = $columns[$testTypeId];

        $data = [
            'school_id'  => $schoolId,
            $column      => $score, 
            'updated_at' => now(),
        ];

        if ($testTypeId == 33) {

            if (!is_null($height) && $height != '') {
               $data['height'] = $height . ' cm';
            }else{
                $data['height'] = '---';
            }

            if (!is_null($weight) && $weight != '') {
                $data['weight'] = $weight . ' kg';
            }else{
                $data['weight'] = '---';
            }
        }

        try {

            DB::table('CwsnTestResultSummary')->updateOrInsert(
                ['student_id' => $studentId, 'term_id' => $termId],
                array_merge($data, ['created_at' => now()])
            );

        } catch (\Illuminate\Database\QueryException $e) {

            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                
                Log::warning('Duplicate CWSN Test Result ignored', [
                    'student_id' => $studentId,
                    'term_id'    => $termId,
                    'test_type'  => $testTypeId,
                ]);

                return;
            }

            throw $e; 
        }
    }

    function timeToMilliseconds($timeStr) {
        if (empty($timeStr)) return 0;

        $parts = explode(':', $timeStr);

        if (count($parts) === 2) {
            $seconds = (int) $parts[0];
            $msPart = $parts[1];

            // Standard timer displays usually show 2 digits for MS (centiseconds, 00-99)
            // e.g., "40:50" = 40s + 500ms
            if (strlen($msPart) === 2) {
                $milliseconds = (int) $msPart * 10;
            } else {
                $milliseconds = (int) $msPart;
            }

            return ($seconds * 1000) + $milliseconds;
        }

        return (int) $timeStr;
    }

    public function CwsnAdminManual($testTypeId) {
        $test = TestTypeMaster::findOrFail($testTypeId);

        $filePath = storage_path('app/public/cwsnpdf/' . $test->TestTypeID);
        if (!file_exists($filePath) && file_exists($filePath . '.pdf')) {
            $filePath = $filePath . '.pdf';
        }

        if (!file_exists($filePath)) {
            abort(404, 'PDF file not found at: ' . $filePath);
        }

        return response()->file($filePath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TrainingManual.pdf"',
            'Cache-Control'       => 'private, max-age=3600, must-revalidate',
        ]);
    }


    private function calculateOneMileResult($studentId, $schoolId, $termId, $timeInMilliseconds, $bmi = null) {

        $student = Sstudent::where('id', $studentId)->select('gender', 'dob')->first();
        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found.',
            ];
        }

        $age = null;

        if (!empty($student->dob)) {
            $age = \Carbon\Carbon::parse($student->dob)->age;
        }

        $bmiResult = DB::table('SeniorTestResults')->where('StudentID', $studentId)->where('SchoolID', $schoolId)
                    ->where('TermId', $termId)->where('TestTypeID', 33)
                    ->orderBy('ResultId', 'desc')
                    ->first();

        if ($bmiResult && $bmiResult->Score !== null && $bmiResult->Score !== '') {
            $bmi = (float) $bmiResult->Score;
        }

        $timeInSeconds = $timeInMilliseconds / 1000;
        $timeInMinutes = $timeInSeconds / 60;

        // Default result
        $vo2Max = null;
        $calculationStatus = 'not_calculated';
        $reason = null;

        /*
         * VO2 max calculation is valid only when:
         */

        if ($timeInSeconds > (13 * 60)) {
            $vo2Max = 1000;   //set default value for Completion time exceeded 13 minutes.
        } elseif ($bmi === null) {
            $vo2Max = 1001;    //set default value for BMI is not available.
        } elseif ($age === null) {
            $vo2Max = 1002;  // Student age cannot be determined.
        } else {

            if (strtolower($student->gender) === 'male') {
                $sex = 1;
            } elseif (strtolower($student->gender) === 'female') {
                $sex = 0;
            } else {
                return [
                    'success' => false,
                    'message' => 'Student gender value is invalid.',
                ];
            }

            // One-mile run/walk VO2 max formula
            $vo2Max = (0.21 * $age * $sex)
                - (0.84 * $bmi)
                - (8.41 * $timeInMinutes)
                + (0.34 * pow($timeInMinutes, 2))
                + 108.94;

            $vo2Max = round($vo2Max, 2);
            $calculationStatus = 'calculated';
        }

        return [
            'success' => true,
            // 'time_in_milliseconds' => $timeInMilliseconds,
            // 'time_in_seconds' => $timeInSeconds,
            // 'time_in_minutes' => $timeInMinutes,

            // 'age' => $age,
            // 'gender' => $student->gender,
            // 'bmi' => $bmi,
            'vo2_max' => $vo2Max,
        ];
    }



}
