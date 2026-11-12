<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;


use App\Models\Sclass;
use App\Models\Teachingthrough;
use App\Models\Skill;
use App\Models\Sport;
use App\Models\Technique;
use App\Models\School;
use App\Models\Activity;
use App\Models\Report;
use DB;
use Response;
use Validator;
use Redirect;
use paginate;
use Session;
use PDF;
use Dompdf\Dompdf;
use App\Models\TermMaster;
use App\Services\DataTableListService;
use App\Exports\TestScoreTemplete;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTestImproperData;
use App\Rules\ExcelTestHeaderValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Jobs\TestDataImportJob;
use App\Imports\ImportFMSTestData;
use App\Imports\ImportFitnessTestData;
use App\Models\TestImportLog;
use Illuminate\Support\Facades\Storage;
use App\Models\Sstudent;
use App\Models\SkillReportSkillTypeTermTypeMapping;
use App\Models\SeniorTestResult;
use Carbon\Carbon;
use Auth;
// use App\Jobs\UpdateFitnessTestResults;
use DataTables;

use App\Traits\ReportHelperTrait;
use App\Traits\UpdateFitnessTestResults;

class AssessorAppController extends Controller
{

	use ReportHelperTrait, UpdateFitnessTestResults;

	public function __construct()
    {
        $this->middleware('restrict.access');
    }

	
	/**
	 * Fitness Assessment Dashbaord.
	 * */
	public function alltests(Request $request) {

		$junior = array(10, 11, 12);
		$junior1 = array(2, 6, 3);		
		$senior = array(8, 9, 5, 4, 15, 3);
		$cwsncatid = [3, 23, 39 ,40];
	
		$juniorData = DB::table('TestCategoryMaster')->whereIn('TestCategoryID',$junior)->orderByRaw('FIELD(TestCategoryID, ' . implode(',', $junior) . ')')->get();
		
		$juniorData1 = DB::table('TestCategoryMaster')->whereIn('TestCategoryID',$junior1)->orderByRaw('FIELD(TestCategoryID, ' . implode(',', $junior1) . ')')->get();
		
		$seniorData = DB::table('TestCategoryMaster')->whereIn('TestCategoryID',$senior)->orderByRaw('FIELD(TestCategoryID, ' . implode(',', $senior) . ')')->get();

		$cwsnCategory = DB::table('TestCategoryMaster')->whereIn('TestCategoryID',$cwsncatid)->orderBy('DisplayOrder')->get();

		
		$title = 'Take Fitness Assessment';

		$userAgent = $request->header('User-Agent');

		$isPureChrome = str_contains($userAgent, 'Chrome') && 
                   !str_contains($userAgent, 'Edg') && 
                   !str_contains($userAgent, 'OPR');

		$isUnsupportedBrowser = str_contains($userAgent, 'Edg') || 
                        str_contains($userAgent, 'OPR') ||
                        str_contains($userAgent, 'Safari') ||
                        str_contains($userAgent, 'Ulaa') ||
                        str_contains($userAgent, 'Firefox');

		if (!$isPureChrome && $isUnsupportedBrowser){
			return view('assessor.alltests', compact('title', 'juniorData', 'juniorData1', 'seniorData', 'cwsnCategory'))->with('warning', 'Some features may not work properly!');
		}


		return view('assessor.alltests', compact('title', 'juniorData', 'juniorData1', 'seniorData','cwsnCategory'));
	}

	
    public function locomotorSkills($TestcategoryId, $reportId, $SeniorBMI = false) {		
		
		$CategoryName = DB::table('TestCategoryMaster')->where('TestCategoryID',$TestcategoryId)->value('TestCategoryName');
			
		$testType = DB::table('TestTypeMaster')->where('TestCategoryID',$TestcategoryId)->where('TestsApplicable',1)->get();
		#$testType = DB::table('TestTypeMaster')->where('TestCategoryID',$TestcategoryId)->get();

		$title = $CategoryName ?? 'Test';	
		$testTypeIds = $testType->pluck('TestTypeID');

		$videos = DB::table('fitness_test_videos')
			->whereIn('testType_id', $testTypeIds)
			->get();
		return view('assessor.locomotorSkills', compact('title','testType', 'TestcategoryId', 'SeniorBMI','videos','testTypeIds'));
	}
	

	public function PhysicalSkills($TestcategoryId, $SeniorBMI = false) {
		
		$CategoryName = DB::table('TestCategoryMaster')->where('TestCategoryID',$TestcategoryId)->value('TestCategoryName');
			
		$testType = DB::table('TestTypeMaster')->where('TestCategoryID',$TestcategoryId)->where('TestsApplicable',2)->get();
		#$testType = DB::table('TestTypeMaster')->where('TestCategoryID',$TestcategoryId)->get();

		$title = $CategoryName ?? 'Test';

		$testTypeIds = $testType->pluck('TestTypeID');

		$videos = DB::table('fitness_test_videos')
			->whereIn('testType_id', $testTypeIds)
			->get();
			
		return view('assessor.locomotorSkills', compact('title','testType', 'TestcategoryId', 'SeniorBMI','videos'));		
	}


    public function FMSTypes($TestTypeId, $SeniorBMI = false) {
		
		$skillReport = DB::table('skill_reports')->select('id','skill_name','TestTypeMasterID')->where('TestTypeMasterID',$TestTypeId)->first();
		
		$skillTypes = DB::table('skill_types')->where('skill_report_id',$skillReport->id)->where('status', 1)->get();
		
		$title             = $skillReport->skill_name;
		$skillReportId     = $skillReport->id;
		$TestTypeMasterID  = $TestTypeId;		
		$userId  = \Auth::id();
		
		
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		


		$classes = DB::table('custom_classes')
		->join('class','class.id','=','custom_classes.class_id')
		->select('custom_classes.id','class_id','section',

			DB::raw("CASE 
                WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                THEN custom_classes.nomenclature 
                ELSE class.name 
            END AS classname")
		)
		->whereIn('class.id', array(1,2,3))
		->Where('school_id', $SchoolId)
		->orderBy('class.id', 'ASC')
		->orderBy('custom_classes.section', 'ASC')
		->get();

		// echo"<pre>";print_r($classes);exit();
		
		
		$seniorclasses = DB::table('custom_classes')
		->join('class','class.id','=','custom_classes.class_id')
		->select('custom_classes.id','class_id','section',

			DB::raw("CASE 
                WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                THEN custom_classes.nomenclature 
                ELSE class.name 
            END AS classname")

			// 'class.name AS classname'
		)
		->whereIn('class.id', array(4,5,6,7,8,9,10,11,12))
		->Where('school_id', $SchoolId)
		->orderBy('class.id', 'ASC')
		->orderBy('custom_classes.section', 'ASC')
		->get();
		
		
		
		
		if($skillReport->skill_name == 'BMI' && $SeniorBMI == false)
		{

			$title = $skillReport->skill_name;
			return view('assessor.input-form.bmi', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
		}
		elseif($skillReport->skill_name == 'BMI' && $SeniorBMI == true)
		{
		
			$classes = $seniorclasses;
			$title = $skillReport->skill_name;
			return view('assessor.input-form.senior-bmi', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
		}
		elseif($skillReport->skill_name == 'Flamingo Balance Test')
		{
			$title = $skillReport->skill_name;
			return view('assessor.input-form.flamingo', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
	    elseif($skillReport->skill_name == 'Plate Tapping')
		{
			$title = $skillReport->skill_name;
			return view('assessor.input-form.plate-tapping', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
		elseif($skillReport->skill_name == 'Push Ups')
		{
			$classes = $seniorclasses;
			
			$title = $skillReport->skill_name;
			return view('assessor.input-form.muscular-endurance', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
		elseif($skillReport->skill_name == 'Partial curl up 30 sec')
		{
			$classes = $seniorclasses;
			
			$title = $skillReport->skill_name;
			return view('assessor.input-form.strength', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
		elseif($skillReport->skill_name == 'Sit and Reach Test')
		{
			$classes = $seniorclasses;
			
			$title = $skillReport->skill_name;
			return view('assessor.input-form.flexibility', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
		elseif($skillReport->skill_name == '50 mt. dash' || $skillReport->skill_name == '600 meter run/walk')
		{
			 
			$classes = $seniorclasses;
			 
			 
			$title = $skillReport->skill_name;
			return view('assessor.input-form.speed', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
			
		}
		else{
			
			return view('assessor.input-form.fms-types', compact('title', 'skillTypes', 'skillReportId', 'TestTypeMasterID', 'classes', 'SchoolId'));
		}
	}



	protected function validateTestInput(Request $request, array $customRules = []) {
		$defaultRules = [
			'student_id'       => 'required_without:students|exists:students,id',
			'skillReportId'    => 'required|exists:skill_reports,id',
			'TestTypeMasterID' => 'required|exists:TestTypeMaster,TestTypeID',
			'SchoolId'         => 'required|exists:schools,id',
		];

		$customMessages = [
	        'students.*.time.required' => 'A valid time is required for all active student lanes.',
	        'students.*.id.exists' => 'One or more selected students are invalid or inactive.',
	    ];

		$validator = Validator::make($request->all(), array_merge($defaultRules, $customRules), $customMessages);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'status'  => 'fail',
				'message' => $validator->errors()->first()
			], 422);
		}

		return null; // Passes validation
	}
	

   	/* Test For Juniors */
	public function SubmitFMSType(Request $request) {

		if ($error = $this->validateTestInput($request, [
			'description'   => 'nullable|array',
			'description.*' => 'nullable|integer'
		])) {
			return $error;
		}


		try{
			$alldata = $request->all();
			$userId  = \Auth::id();
			if(Session::get('SelectSchoolId')) {
				$SchoolId = Session::get('SelectSchoolId');
			}else{
				$SchoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
			$TermMasterId = $this->getTermId($SchoolId);
			
			
			$descriptions = $alldata['description'] ?? [];
			if(!empty($descriptions)) {
				foreach($descriptions as $key =>$val)	{
					$TypeMapping = new SkillReportSkillTypeTermTypeMapping();
					$TypeMapping->school_id         = $alldata['SchoolId'];
					$TypeMapping->term_master_id    = $TermMasterId;
					$TypeMapping->student_id        = $alldata['student_id'];
					$TypeMapping->skill_report_id   = $alldata['skillReportId'];
					$TypeMapping->skill_type_id     = $val;
					$TypeMapping->skill_type_value  = !empty($val)?'Y':'';
					$TypeMapping->term_type_id      =  1;
					$TypeMapping->status      		=  1;
					$TypeMapping->created_by        =  $userId;
					$TypeMapping->created_at        = now();
					$TypeMapping->updated_at        = now();
					$TypeMapping->save();		
					// echo"<pre>";print_r($TypeMapping);exit();			
				}
			}else{
				
			
				$TypeMapping = new SkillReportSkillTypeTermTypeMapping();
				$TypeMapping->school_id         = $alldata['SchoolId'];
				$TypeMapping->term_master_id    = $TermMasterId;
				$TypeMapping->student_id        = $alldata['student_id'];
				$TypeMapping->skill_report_id   = $alldata['skillReportId'];
				$TypeMapping->skill_type_id     = null;
				$TypeMapping->skill_type_value  = null;
				$TypeMapping->term_type_id      =  1;
				$TypeMapping->status      		=  1;
				$TypeMapping->created_by        =  $userId;
				$TypeMapping->created_at        = now();
				$TypeMapping->updated_at        = now();
				$TypeMapping->save();
			}
			$score = count($descriptions) . ' / 5';	

			$this->UpdateLowerTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'], $score, $alldata['SchoolId'], null, null);
			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);

			return response()->json(['status' => 'success', 'message' => $message], 200);
		}catch (\Exception $e) {
			return response()->json([
				'status' => 'fail',
				'message' => 'Test record not submitted. Please try again later.'
			], 500);
		}					
	}

 
	/**
	 * Store BMI Score For Junior and Senior.
	 * */
    public function SubmitBMIRecord(Request $request) {

    	if ($error = $this->validateTestInput($request, [
			'height'   => 'required|numeric|min:30|max:250',
			'weight'   => 'required|numeric|min:5|max:300',
			'testtype' => 'required|string|in:seniorbmi,juniorbmi'
		])) {
			return $error;
		}
	   
	    $alldata = $request->all();
		$userId  = \Auth::id();
	
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		$TermMasterId = $this->getTermId($SchoolId);
		

		$student = Sstudent::where('id',$alldata['student_id'])->select('gender','dob')->first();
		$dob = Carbon::parse($student->dob);
	    $studentAge = $dob->age;
	    $studentGender = $student->gender;
	    $ageGender = $studentAge . strtolower(substr($studentGender, 0, 1)); 

	
		if($alldata['height'] !='' &&  $alldata['weight'] !='') {
				
		    $HeightMM = $alldata['height'] / 100; 
			$BMI = $alldata['weight'] / ($HeightMM * $HeightMM);


			/* SP for calculating BMI benchmark */
			$result = DB::selectOne("CALL GetBmiLevel(?, ?)", [$ageGender, $BMI]); 

		
			$Result = new SeniorTestResult();
			
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->Score   	  = $BMI;
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->height       = $alldata['height'];
			$Result->weight       = $alldata['weight'];
			$Result->level        = $result->level_code;
			$Result->save();
		
			$BMIValue = round($BMI, 2);
			// $BMIDisplay = $BMIValue. ' kg/m²';
			if($request->input('testtype') === 'seniorbmi'){

				/*UpdateFitnessTestResults::dispatch($alldata['student_id'],$TermMasterId, $alldata['skillReportId'],$BMIDisplay,$alldata['SchoolId'], $alldata['height'], $alldata['weight']);*/

				$this->UpdateSeniorTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'] , $BMIValue ,$alldata['SchoolId'],$alldata['height'], $alldata['weight']);

			}

			
			if($request->input('testtype') === 'juniorbmi') {
				
				$this->UpdateLowerTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'], $BMIValue, $alldata['SchoolId'], $alldata['height'], $alldata['weight']);
			}

			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);

			
		} else {
			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
    }
   
  	public function timeToMilliseconds($time){
		if (!$time) {
			return 0;
		}

		$parts = preg_split('/[:.]/', $time);

		$minutes = (int)($parts[0] ?? 0);
		$seconds = (int)($parts[1] ?? 0);
		$milliseconds = (int)($parts[2] ?? 0);

		return ($minutes * 60 * 1000) + ($seconds * 1000) + $milliseconds;
	}

	/* Test For Juniors */
    public function SubmitFlamingoRecord(Request $request) {
	   	
	   	if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID' => 'required|exists:TestTypeMaster,TestTypeID',
			'timetaken'        => 'nullable|string',
			'total_number'     => 'required|integer|min:0|max:100'
		])) {
			return $error;
		}

	    $alldata = $request->all();
		$userId  = \Auth::id();		
	
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}

		$TermMasterId = $this->getTermId($SchoolId);

		$checktime = $this->timeToMilliseconds($alldata['timetaken']);
		$checkSeconds = intval($checktime / 1000);
		$pauseCount = $alldata['total_number'];

		if ($checkSeconds < 30 && $alldata['total_number'] > 14) {
		    $alldata['total_number'] = 1000;
		}else if ($checkSeconds < 60 && $alldata['total_number'] > 26) {
		    $alldata['total_number'] = 500;
		}else{
			$alldata['total_number'] = $pauseCount;

		}


		if($alldata['total_number'] >='00' && $alldata['student_id'] !='') 	{

		    $levels = $this->GetFitnessTestLevel($alldata['student_id'], $alldata['TestTypeMasterID'], $alldata['total_number'], 'Less_is_better');
			$Result = new SeniorTestResult();
			
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->Score   	  = $alldata['total_number'];
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->level        = $levels;
			$Result->save();
			
			// $score = $alldata['total_number'] .' times';
			$this->UpdateLowerTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'], $alldata['total_number'] , $alldata['SchoolId'], null, null);

			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);
			
		}else
		{
			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
    }
  

   	/* Test For Juniors */
    public function SubmitPlateTappingRecord(Request $request) {
	   
	    if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID'  => 'required|exists:TestTypeMaster,TestTypeID',
			'total_miliseconds' => 'required|numeric|min:1000|max:600000'
		])) {
			return $error;
		}

		$alldata = $request->all();
		$userId  = \Auth::id();
		

		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);
		

		if($alldata['total_miliseconds'] !='' && $alldata['student_id'] !='')
		{

			$milliseconds = $alldata['total_miliseconds']; 
			$seconds = $milliseconds / 1000; 		    
		    $levels = $this->GetFitnessTestLevel($alldata['student_id'], $alldata['TestTypeMasterID'], $milliseconds, 'Less_is_better');

			$Result = new SeniorTestResult();
			
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->Score   	  = $alldata['total_miliseconds'];
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->level        = $levels;
			$Result->save();
			
			// $score = $seconds .' Sec';
			$this->UpdateLowerTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'], $alldata['total_miliseconds'], $alldata['SchoolId'], null, null);


			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);
			
		}else
		{
			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
    }

    /* 1. Test For Seniors (50 Mt. and 600 Mt.) */
    public function SubmitSpeedRecord(Request $request) {

    	if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID' => 'required|exists:TestTypeMaster,TestTypeID',
			'students'         => 'required|array|min:1',
			'students.*.id'    => 'required|exists:students,id',
		])) {
			return $error;
		}

		$alldata = $request->all();
		$userId  = \Auth::id();
		
		$students = $request->students ?? [];		

		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);
		
		if(count($students) >0)	{
			
			$studentIdsArray = [];
			foreach($students as $key => $val) {
				
				$studentId = $val['id'];

				$studentIdsArray[] = $val['id']; 
				$studentTime = $val['time'];

				$studentTimeInSec = $studentTime / 1000;
				
				$levels = $this->GetFitnessTestLevel($studentId, $alldata['TestTypeMasterID'], $studentTime, 'Less_is_better');


				$Result = new SeniorTestResult();					
				$Result->SchoolID     = $alldata['SchoolId'];
				$Result->TermId       = $TermMasterId;
				$Result->TestTypeID   = $alldata['skillReportId'];
				$Result->StudentID    = $studentId;
				$Result->Score   	  = $studentTime;
				$Result->created_at   = now();
				$Result->CreatedBy    = $userId;
				$Result->updated_at   = now();
				$Result->ModifiedBy   = $userId;
				$Result->level        = $levels ?? '';
				$Result->save();

				// $score = $studentTimeInSec .' Sec';
				$this->UpdateSeniorTestStatus($studentId, $TermMasterId, $alldata['skillReportId'] , $studentTime , $alldata['SchoolId'], null, null);

				/*UpdateFitnessTestResults::dispatch($studentId,$TermMasterId, $alldata['skillReportId'],$score,$alldata['SchoolId'], null, null);*/

				$messages[] = $this->TestMessage($studentId, $alldata['skillReportId']);
			}


			$message = $this->TestMessage($studentIdsArray, $alldata['skillReportId']);
			return response()->json(['success' => true, 'message' => $message]);

			
			
		} else {

			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
	}

	/* 2. Test For Seniors */
    public function SubmitPushUpRecord(Request $request) {
		
		if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID' => 'required|exists:TestTypeMaster,TestTypeID',
			'total_push_up'    => 'required|integer|min:0|max:200'
		])) {
			return $error;
		}

		$alldata = $request->all();
		$userId  = \Auth::id();
					
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);
		
		if($alldata['total_push_up'] !='' && $alldata['student_id'] !='') {

			$levels = $this->GetFitnessTestLevel($alldata['student_id'], $alldata['TestTypeMasterID'], $alldata['total_push_up'], 'More_is_better');

			$Result = new SeniorTestResult();	
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->Score   	  = $alldata['total_push_up'];
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->level        = $levels;
			$Result->save();
	

			// $score = $alldata['total_push_up'] .' times'; 	

			$this->UpdateSeniorTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'] , $alldata['total_push_up'] ,$alldata['SchoolId'] , null, null);

			/*UpdateFitnessTestResults::dispatch($alldata['student_id'],$TermMasterId, $alldata['skillReportId'], $score ,$alldata['SchoolId'], null, null);*/

			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);
			
		}else {

			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
	}		
	
	/* 3. Test For Seniors */
	public function SubmitPartialCurlUpRecord(Request $request)	{
		
		if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID'   => 'required|exists:TestTypeMaster,TestTypeID',
			'count_total_number' => 'required|integer|min:0|max:150'
		])) {
			return $error;
		}

		$alldata = $request->all();
		$userId  = \Auth::id();	
	
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);
		
		
		if($alldata['count_total_number'] !='' && $alldata['student_id'] !='')	{

		
			$levels=$this->GetFitnessTestLevel($alldata['student_id'], $alldata['TestTypeMasterID'], $alldata['count_total_number'], 'More_is_better');

			$Result = new SeniorTestResult();
			
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->Score   	  = $alldata['count_total_number'];
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->level        = $levels;
			$Result->save();
				
			// $score = $alldata['count_total_number'] .' times';

			$this->UpdateSeniorTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'], $alldata['count_total_number'], $alldata['SchoolId'] , null, null);

			/*UpdateFitnessTestResults::dispatch($alldata['student_id'],$TermMasterId, $alldata['skillReportId'], $score,$alldata['SchoolId'], null, null);*/
			
			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);
			
		} else { 

			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
	}
	
	/* 4.  Test For Seniors */
	public function SubmitSitAndReachRecord(Request $request) {
		
		if ($error = $this->validateTestInput($request, [
			'TestTypeMasterID' => 'required|exists:TestTypeMaster,TestTypeID',
			'result'           => 'required|numeric|min:-50|max:100',
			'initial_cm'       => 'nullable|numeric',
			'final_cm'         => 'nullable|numeric'
		])) {
			return $error;
		}


		$alldata = $request->all();
		$userId  = \Auth::id();
		
		
		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);
		
		
		if($alldata['result'] !='' && $alldata['student_id'] !='') 	{

			$studentScore = $alldata['result'] / 10;
			$levels = $this->GetFitnessTestLevel($alldata['student_id'], $alldata['TestTypeMasterID'], $alldata['result'], 'More_is_better');

			$Result = new SeniorTestResult();
			
			$Result->SchoolID     = $alldata['SchoolId'];
			$Result->StudentID    = $alldata['student_id'];
			$Result->TermId       = $TermMasterId;
			$Result->TestTypeID   = $alldata['skillReportId'];
			$Result->InitialScore = $alldata['initial_cm'];
			$Result->FinalScore   = $alldata['final_cm'];
			$Result->Score   	  = $alldata['result'];
			$Result->created_at   = now();
			$Result->CreatedBy    = $userId;
			$Result->updated_at   = now();
			$Result->ModifiedBy   = $userId;
			$Result->level        = $levels;
			$Result->save();
				
			$score = $studentScore .' cm';
			$this->UpdateSeniorTestStatus($alldata['student_id'], $TermMasterId, $alldata['skillReportId'] ,$alldata['result'] ,$alldata['SchoolId'] , null, null);
			$message = $this->TestMessage($alldata['student_id'],$alldata['skillReportId']);
			return response()->json(['success' => true,'message' => $message]);
			
		} else {
			return response()->json(['success' => false,'message' => 'Something went wrong.']);
		}
		
	}
	
	protected function TestMessage($studentIds, $testTypeId) {

	    $testNames = [
	        1  => 'Running',
	        2  => 'Hopping',
	        3  => 'Jumping & Landing',
	        4  => 'One Foot Balance',
	        5  => 'Skipping',
	        6  => 'Dodging',
	        7  => 'Catching & Receiving Bounce Ball',
	        8  => 'Catching Small Ball With Two Hands',
	        9  => 'Under Arm Throw',
	        10 => 'Over Arm Throw',
	        11 => 'Striking drop & hit forward',
	        12 => 'Dribbling With Hands',
	        13 => 'Dribbling With Feet',
	        14 => 'Kicking Stationary Ball',
	        15 => 'Beam Walk',
	        16 => 'Plate Tapping',
	        17 => 'Flamingo Balance',
	        18 => 'BMI',
	        19 => '50 Mt. Dash',
	        20 => '600 meter run/walk',
	        21 => 'Partial curl up 30 sec',
	        22 => 'Sit and Reach Test',
	        23 => 'Push Ups'
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


	public function getStudentsRoll(Request $request) {


		$userId = Auth::id();

		if(Session::get('SelectSchoolId')) {
			$SchoolId = Session::get('SelectSchoolId');
		}else{
			$SchoolId = DB::table('school_trainers')
			->join('schools','schools.id','=','school_trainers.school_id')
			->where('school_trainers.trainer_id',$userId)
			->where('school_trainers.status', 1)
			->value('school_trainers.school_id');
		}
		
		$TermMasterId = $this->getTermId($SchoolId);

	    $school = School::find($SchoolId);
		
	    $query = $request->get('query');
	    $classCustom = $request->get('class_id'); 

		$testStatus = $request->test_status;
		$skillReportId = $request->skillReportId;
		$testType = $request->testType;

	    if (!$classCustom) {
	        return response()->json([]);
	    }

		$termMasterId =  $this->getTermId($SchoolId);
		// echo"<pre>";print_r($termMasterId);exit();

	    [$customClassId, $classId, $sectionId] = explode('-', $classCustom, 3);
		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;


		$getData =  DB::table('students')
				->where('school_id', (string) $SchoolId)
				->where('academic_year', $academicYear)
				->where('status','active')
				->where('class_id', $classId)
				->where('custom_class_id', $customClassId)
				->where('section_id', $sectionId);


		if($testType == 'cwsnlist'){
			$getData->where('students.is_pwd', '1')
			->join('student_rpwd_mapping', 'students.id', '=', 'student_rpwd_mapping.student_id')
	        ->join('pwd_types', 'student_rpwd_mapping.pwd_type_id', '=', 'pwd_types.id');
		}


		$select = [
	        'students.id',
	        'students.rollno',
	        'students.student_name',
	        'students.user_id',
	    ];

	    if ($testType === 'cwsnlist') {
	        $select[] = 'students.is_pwd';
	        $select[] = 'pwd_types.disability_type';
	    }

		if ($testStatus == "all" && $testType != "cwsnlist") {	

			$students = $getData
			->when((int) $classId >= 5, function ($query) {
		  
		            $query->where('students.is_pwd', '0');
		        })
			->select($select)->orderBy('rollno', 'asc')->get();

		} else if ($testStatus == "remaining" && $testType == "allFmsTest") {
			$students = $getData->whereNotExists(function ($query) use ($skillReportId,$termMasterId) {
					$query->select(DB::raw(1))
						->from('skillreport_skilltype_termtype_mapping as mapping')
						->whereRaw('mapping.student_id = students.id')
						->where('mapping.skill_report_id', '=', $skillReportId)
						->where('mapping.term_master_id', '=', $termMasterId);
				})
				// ->select('students.id', 'students.rollno', 'students.student_name', 'students.user_id')
				->select($select)->distinct()
				->orderBy('students.rollno', 'asc')
				->get();

		} else if ($testStatus == "remaining" && $testType == "cwsnlist") {
 			
			$categoryId = (int) $request->input('cwsn_type');
			$students = $getData->whereNotExists(function ($query) use ($termMasterId, $categoryId ) {
		        $query->select(DB::raw(1))
		            ->from('SeniorTestResults as mapping')
		            ->join('skill_reports', 'skill_reports.id', '=', 'mapping.TestTypeID')
		            ->join('TestTypeMaster', 'TestTypeMaster.TestTypeID', '=', 'skill_reports.TestTypeMasterID')
		            ->where('TestTypeMaster.TestCategoryID', '=', $categoryId)
		            ->whereRaw('mapping.StudentID = students.id')
		            ->where('mapping.TermId', '=', $termMasterId);
		    })
		    ->select($select)
		    ->distinct()
		    ->orderBy('students.rollno', 'asc')
		    ->get();

		} else if ($testStatus == "all" && $testType == "cwsnlist") {

			$students = $getData->select($select)->distinct()->orderBy('students.rollno', 'asc')->get();

		} else {
			
			$students = $getData
				->when((int) $classId >= 5, function ($query) {
		            $query->where('students.is_pwd', '0');
		        })
			->whereNotExists(function ($query) use ($skillReportId,$termMasterId) {
				$query->select(DB::raw(1))
					->from('SeniorTestResults as mapping')
					->whereRaw('mapping.StudentID = students.id')
					->where('mapping.TestTypeID', '=', $skillReportId)
					->where('mapping.TermId', '=', $termMasterId);
			})
			->select($select)->distinct()
			->orderBy('students.rollno', 'asc')
			->get();
			/*

			$students = $getData
		        ->when((int) $classId >= 6, function ($query) {
		            // Class 6-12: only normal students
		            $query->where('students.is_pwd', '0');
		        })
		        ->whereNotExists(function ($query) use ($skillReportId, $termMasterId) {
		            $query->select(DB::raw(1))
		                ->from('SeniorTestResults as mapping')
		                ->whereRaw('mapping.StudentID = students.id')
		                ->where('mapping.TestTypeID', $skillReportId)
		                ->where('mapping.TermId', $termMasterId);
		        })
		        ->select($select)
		        ->distinct()
		        ->orderBy('students.rollno', 'asc')
		        ->get();
		        */
				
		}

	    return response()->json($students);
	}


	public function fetchStudentDetail(Request $request) {
		
		$classId = $request->class_id;
		$customClassId = $request->custom_class_id;
		$rollNo = $request->roll_no;
		$student_id = $request->studentId;
		$skillReportId = $request->skillReportId;
		$testType = $request->testType;
		$SchoolId = $request->school_id;
		$school = School::find($SchoolId);
		
		$student_reg_no = $request->student_reg_no;
		
		if($student_reg_no){
			$scan_classes = $request->scan_classes;
			$classIds = collect($scan_classes)->pluck('class_id')->toArray();

			$existsInSchool = DB::table('students')
            ->where('user_id', 'LIKE', "%{$student_reg_no}%")
            ->where('school_id', $SchoolId)
            ->exists();

			if (!$existsInSchool) {
				return response()->json(['error' => false,'message' => 'You/Student are not authorised']);
			}

			$existsInClass = DB::table('students')
				->where('user_id', 'LIKE', "%{$student_reg_no}%")
				->whereIn('class_id', $classIds)
				->exists();

			if (!$existsInClass) {
				return response()->json(['error' => false,'message' => 'Student not eligible for test.']);
			}	
			if (is_string($scan_classes)) {
				$scan_classes = json_decode($scan_classes, true);
			}

			$student = DB::table('students')
			->where('students.user_id', 'LIKE', "%{$student_reg_no}%")
			->join('custom_classes', 'students.class_id', '=','custom_classes.class_id')
			->join('class', 'custom_classes.class_id', '=', 'class.id')
			->join('schools', 'students.school_id', '=', 'schools.id')
			->where('students.status', 'active')
			->where('students.school_id', $SchoolId)
			->whereIn('students.class_id', $classIds)
			->select(
				'students.id',
				'students.user_id',
				'students.rollno',
				'students.school_id',
				'students.school_code',
				'students.student_name',
				'students.gender',
				'students.dob',
				'custom_classes.id as custom_class_id',
				'custom_classes.section',
				DB::raw("
					CASE 
						WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
						THEN custom_classes.nomenclature 
						ELSE class.name 
					END AS classname
				")
			)
			->first();

			$cls = $student->classname;
			$sec = $student->section;
			$className = $cls.'-'.$sec;
		}else{
			
			$className = $request->class_name;
			$student =  DB::table('students')
			->where('id', $student_id)
			->select('id', 'rollno','user_id' ,'school_id','school_code','student_uid','student_name','gender','custom_class_id','class_id','section_id','dob')->where('status','active')
			->first();

			if($request->input('testType') == 'cwsnlist'){

				$student =  DB::table('students')
				->join('student_rpwd_mapping', 'student_rpwd_mapping.student_id', 'students.id')
				->leftJoin('anthropometric_table as ht', 'ht.id', '=', 'student_rpwd_mapping.anthropo_ht_id')
	    		->leftJoin('anthropometric_table as wt', 'wt.id', '=', 'student_rpwd_mapping.anthropo_wt_id')

				->where('students.id', $student_id)
				->select('students.id', 
					'students.rollno',
					'students.user_id' ,
					'students.school_id',
					'students.school_code',
					'students.student_uid',
					'students.student_name',
					'students.gender',
					'students.custom_class_id',
					'students.class_id',
					'students.section_id',
					'students.dob',
					'student_rpwd_mapping.anthropo_ht_id',
					'student_rpwd_mapping.anthropo_wt_id',

					'ht.anthropometric_value as height_value',
			        'ht.anthropometric_type as height_type',
			        
			        // Weight values
			        'wt.anthropometric_value as weight_value',
			        'wt.anthropometric_type as weight_type'
				)
				->where('students.status','active')
				->first();
			}

		}
		
		if ($student) {

			$dob = $student->dob ;
			$age = Carbon::parse($dob)->age;
			$schoolId = $student->school_id;
			$termMasterId =  $this->getTermId($schoolId);


			$existingTestName = [];
			$testExists = false;

	        switch ($testType) {
	        	case 'allFmsTest':
	        		$testExists = DB::table('skillreport_skilltype_termtype_mapping')   
	                ->where('student_id', $student->id)
	                ->where('school_id', $schoolId)
	                ->where('term_master_id', $termMasterId)
	                ->where('skill_report_id', $skillReportId)
	                ->exists();
	        		break;

	        	case 'fitnessTest':
	        		$testExists = DB::table('SeniorTestResults')    
	                ->where('StudentID', $student->id)
	                ->where('SchoolID', $schoolId)
	                ->where('TermId', $termMasterId)
	                ->where('TestTypeID', $skillReportId)
	                ->exists();
	        		break;

	        	case 'cwsnlist':

	                $targetCategory = DB::table('skill_reports')
			        ->join('TestTypeMaster', 'TestTypeMaster.TestTypeID', '=', 'skill_reports.TestTypeMasterID')
			        ->where('skill_reports.id', $skillReportId)
			        ->select('TestTypeMaster.TestCategoryID')
			        ->first();
	             
	                if ($targetCategory) {

				        $existingRecord = DB::table('SeniorTestResults')
				            ->join('skill_reports', 'skill_reports.id', '=', 'SeniorTestResults.TestTypeID')
				            ->join('TestTypeMaster', 'TestTypeMaster.TestTypeID', '=', 'skill_reports.TestTypeMasterID')
				            ->where('SeniorTestResults.StudentID', $student->id)
				            ->where('SeniorTestResults.SchoolID', $schoolId)
				            ->where('SeniorTestResults.TermId', $termMasterId)
				            ->where('TestTypeMaster.TestCategoryID', $targetCategory->TestCategoryID)
				            ->select('skill_reports.skill_name as test_name')
				            ->first();

				        if ($existingRecord) {
				            $testExists = true;
				            $existingTestName = [
				            	'test_category' => DB::table('TestCategoryMaster')->where('TestCategoryID',$targetCategory->TestCategoryID)->value('TestCategoryName'),
				            	'test_name' => $existingRecord->test_name,
				        	];

				        }
				    }

	        		break;

	        	default:
	        		
	        		break;
	        }

			return response()->json([
				'success' => true,
				'data' => [
					'name' => $student->student_name,
					'class_name' => $className ?? 'N/A',
					'student_registration_no' => $student->user_id ?? 'N/A',
					'student_id' => $student->id ?? 'N/A',
					'Age' => $age ?? 'N/A',
					'Gender' => $student->gender ?? 'N/A',
					'student_roll_no' =>$student->rollno ?? 'N/A',

					'test_already_given' => $testExists ? true : false,
					'existingTestName' => $existingTestName,

					'anthropo_ht_id' => $student->anthropo_ht_id ?? null,
					'anthropo_wt_id' => $student->anthropo_wt_id ?? null,
					'height_value'  =>  $student->height_value ?? null,
					'height_type'  =>  $student->height_type ?? null,

					'weight_value'  =>  $student->weight_value ?? null,
					'weight_type'  =>  $student->weight_type ?? null,
				]
			]);
		} else 	{
			return response()->json(['success' => false,'message' => 'The student is not found in selected class.']);
		}
    }


	public function clearExistingRecords(Request $request) {

	    $studentId = $request->student_id;
	    $classId   = $request->class_id;
	    $testType = $request->testType;

	    try {

	        $schoolId = $request->school_id;
			$termMasterId =  $this->getTermId($schoolId);

			switch ($testType) {
	        	case 'allFmsTest':
	        		$testExists = DB::table('skillreport_skilltype_termtype_mapping')
	                ->where('student_id', $studentId)
	                ->where('school_id', $schoolId)
	                ->where('term_master_id', $termMasterId)
	                ->where('skill_report_id', $request->skillReportId)
	                ->delete();
	                $this->UpdateLowerTestStatus($studentId, $termMasterId, $request->skillReportId, null , $schoolId, null, null);
	        		break;

	        	case 'fitnessTest':
	        		$testExists = DB::table('SeniorTestResults')
	                ->where('StudentID', $studentId)
	                ->where('SchoolID', $schoolId)
	                ->where('TermId', $termMasterId)
	                ->where('TestTypeID', $request->skillReportId)
	                ->delete();

	                if($request->skillReportId == 18){

	                	if (in_array($request->class_id, [1, 2, 3])) {
	                		$this->UpdateLowerTestStatus($studentId, $termMasterId, $request->skillReportId, null, $schoolId, '', '');
	                	}
	          			$this->UpdateSeniorTestStatus($studentId, $termMasterId, $request->skillReportId ,null ,$schoolId, '', '');


	                	/*UpdateFitnessTestResults::dispatch($studentId,$termMasterId, $request->skillReportId, null, $schoolId, '', '');
	                    $this->UpdateLowerTestStatus($studentId, $termMasterId, $request->skillReportId, null, $schoolId, '', '');*/

	                }else{

	                    if (in_array($request->class_id, [1, 2, 3])) {
	                		$this->UpdateLowerTestStatus($studentId, $termMasterId, $request->skillReportId, null , $schoolId, null, null);
	                	}
	                    $this->UpdateSeniorTestStatus($studentId, $termMasterId, $request->skillReportId ,null ,$schoolId, null, null);

	                    /*UpdateFitnessTestResults::dispatch($studentId,$termMasterId, $request->skillReportId, null, $schoolId, null, null);
	                    $this->UpdateLowerTestStatus($studentId, $termMasterId, $request->skillReportId, null, $schoolId, null, null);*/
	                }
	                
	        		break;
	        	case 'cwsnlist':

	          		$skill_reports_id = (int) $request->input('skillReportId');

	           		$categoryInfo = DB::table('skill_reports')
			        ->join('TestTypeMaster', 'TestTypeMaster.TestTypeID', '=', 'skill_reports.TestTypeMasterID')
			        ->where('skill_reports.id', $skill_reports_id)
			        ->select('TestTypeMaster.TestCategoryID')
			        ->first();
			   
	        		if ($categoryInfo) {	

	        			if( (int) $request->skillReportId === 33){

				        	DB::table('SeniorTestResults')->where('StudentID', $studentId)
				        	->where('TermId', $termMasterId)->where('TestTypeID', 32)
	                        ->update([
	                            'Score' => 1001,
	                            'updated_at' => now()
	                        ]);

	                        $this->UpdateCWSNTestStatus($studentId, $termMasterId, 32, null, $request->school_id, '', '');
				        }

				        DB::table('SeniorTestResults')
			            ->where('StudentID', $studentId)
			            ->where('TermId', $termMasterId)
			            ->whereIn('TestTypeID', function($query) use ($categoryInfo) {
			                $query->select('skill_reports.id')
			                      ->from('skill_reports')
			                      ->join('TestTypeMaster', 'TestTypeMaster.TestTypeID', '=', 'skill_reports.TestTypeMasterID')
			                      ->where('TestTypeMaster.TestCategoryID', $categoryInfo->TestCategoryID);
			            })->delete();
				    }


	                $this->UpdateCWSNTestStatus($studentId, $termMasterId, $request->skillReportId, null, $request->school_id, '', '');
	                break;
				
	        	default:
	        		
	        		break;
	        }

	        return response()->json([
	            'success' => true,
	            'message' => 'Old test record has been removed. You can now retake the test.'
	        ]);

	    } catch (\Exception $e) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Error while deleting test records: ' . $e->getMessage()
	        ], 500);
	    }
	}


    /**
	 * 02-09-2025
	 * Get Fitness Level Based on Score and Benchmark Using MySQL Function. 
	 * */
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
	
	public function uploadTestData(Request $request, DataTableListService $dataTable){

		$title = "Upload Test Data";
		$userId = Auth::id();

		if (!in_array($userId, [3139, 3251, 3257])) {
			return redirect()->route('filldart.dashboard');
		}
		
		$SchoolId = Session::get('SelectSchoolId') ?? DB::table('school_reference')
		->where('school_user_id',$userId)
		->where('status', 1)
		->value('school_id');

		$skillIds = DB::table('skill_reports')->get();

		$junior = [10, 11, 12, 2, 6, 3];
		$senior = [8, 9, 5, 4, 15, 3];

		$juniorData = DB::table('TestCategoryMaster')
			->join('TestTypeMaster', 'TestTypeMaster.TestCategoryID', '=', 'TestCategoryMaster.TestCategoryID')
			->join('skill_reports', 'skill_reports.TestTypeMasterID', '=', 'TestTypeMaster.TestTypeID')
			->whereIn('TestTypeMaster.TestsApplicable', [1,2])
			->whereIn('TestCategoryMaster.TestCategoryID', $junior)
			->select([
				'TestCategoryMaster.TestCategoryID',
				'TestCategoryMaster.TestCategoryName',
				'TestCategoryMaster.TestCategoryImage',
				'TestTypeMaster.TestTypeID',
				'TestTypeMaster.TestTypeName',
				'skill_reports.id as SkillReportID',
				'skill_reports.skill_name',
				'skill_reports.TestTypeMasterID'
			]);

		$seniorData = DB::table('TestCategoryMaster')
			->join('TestTypeMaster', 'TestTypeMaster.TestCategoryID', '=', 'TestCategoryMaster.TestCategoryID')
			->join('skill_reports', 'skill_reports.TestTypeMasterID', '=', 'TestTypeMaster.TestTypeID')
			->where('TestTypeMaster.TestsApplicable', 2)
			->whereIn('TestCategoryMaster.TestCategoryID', $senior)
			->select([
				'TestCategoryMaster.TestCategoryID',
				'TestCategoryMaster.TestCategoryName',
				'TestCategoryMaster.TestCategoryImage',
				'TestTypeMaster.TestTypeID',
				'TestTypeMaster.TestTypeName',
				'skill_reports.id as SkillReportID',
				'skill_reports.skill_name',
				'skill_reports.TestTypeMasterID'
			]);
			
		
		$dataQuery = $juniorData->union($seniorData);
		$orderCategories = array_merge($junior, $senior);

		$combinedDataQuery = DB::query()
			->fromSub($dataQuery, 'combined')
			->orderByRaw('FIELD(TestCategoryID, ' . implode(',', $orderCategories) . ')')->orderBy('SkillReportID');

		$filters = [
			'skills' => function ($combinedDataQuery, $value) {
				$combinedDataQuery->where('SkillReportID', $value);
			},
		];


		$juniorClasses = [1,2,3];
		$seniorclasses = [4,5,6,7,8,9,10,11,12];

		if ($request->ajax()) {
			return $dataTable
				->setQuery($combinedDataQuery)
				->setFilters($filters)
				->setSearchableColumns(['skill_name'])
				->setSortableColumns(['skill_name' => 'skill_name'])
				->enableDefaultOrdering (false)
				->addCustomColumn('downloadTemplate', function ($row) {
					$id = $row->SkillReportID;
					return '<a type="button" href="javascript:void(0);" class="btn btn-primary btn-sm text-light w-40 download-template" 
								data-id="' . $id . '">
								<i class="fa-solid fa-download"></i> Template</a>';
				})
				->addCustomColumn('downloadSample', function ($row) {
					$id = $row->SkillReportID;
					return '<a type="button" href="javascript:void(0);" class="btn btn-success btn-sm text-light w-40 download-sample" 
								data-id="' . $id . '">
								<i class="fa-solid fa-download"></i> Sample</a>';
				})
				->addCustomColumn('classType', function ($row) use ($junior, $senior, $juniorClasses, $seniorclasses) {

					
					if ($row->TestCategoryID == 3) {

						$allClasses = array_merge($juniorClasses, $seniorclasses);
						$badges = '';
						foreach ($allClasses as $class) {
							$badges .= '<span class="badge p-2 m-1"
								style="border: 1px solid #6c757d; font-size: 13px; font-weight: 500;"> Class-'
								. $class .
							'</span>';
						}
						return $badges;
					}

					if (in_array($row->TestCategoryID, $junior)) {
						// return implode(', ', $juniorClasses);
						$badges = '';
						foreach ($juniorClasses as $class) {
							$badges .= '<span class="badge p-2 m-1"
								style="border: 1px solid #6c757d; font-size: 13px; font-weight: 500;"> Class-'
								. $class .
							'</span>';
						}
						return $badges;
					}
					if (in_array($row->TestCategoryID, $senior)) {
						$badges = '';
						foreach ($seniorclasses as $class) {
							$badges .= '<span class="badge p-2 m-1"
								style="border: 1px solid #6c757d; font-size: 13px; font-weight: 500;"> Class-'
								. $class .
							'</span>';
						}
						return $badges;
					}

					return '';
				})

			->render($request);
		}

		$logs = TestImportLog::with('user')
        ->where('user_id', Auth::id())->where('is_active', 'active')
        ->orderBy('created_at', 'desc')
        ->get();

		return view('assessor.upload-test-data', compact('title', 'skillIds', 'SchoolId', 'logs'));
	}

	public function testScoreSample(Request $request) {
		$skillId = $request->skillId;
		$skill = DB::table('skill_reports')
			->where('id', $skillId)
			->first();
        $originalFileName = 'Test_Sample_' . $skillId . '.xlsx';
    	$filePath = public_path('downloads/SampleTemplates/' . $originalFileName);

		if (!file_exists($filePath)) {
			return response()->json([
				'message' => 'Sample file not found for this skill.'
			], 404);
		}
		$fileName = preg_replace('/[^A-Za-z0-9_-]/', '_', $skill->skill_name) . '_Test_Sample.xlsx';
	

		$headers = [
			'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		];

		return response()->download($filePath, $fileName, $headers);
    }

	private function getClassesBySkill($skillId){
		
		if ($skillId >= 1 && $skillId <= 17) {
			return [1, 2, 3];
		}

		if ($skillId == 18) {
			return range(1, 12);
		}

		return range(4, 12);
	}

	public function downloadTestTemplete(Request $request) {
	  
	    try {

	        set_time_limit(0);
	        ini_set('memory_limit', '1024M');

	        $skillIds = $request->input('skillIds', []);
	        $schoolId = $request->input('schoolId');
	        $status = $request->input('status');

	        if (empty($skillIds)) {
	            return response()->json(['error' => 'No skills selected'], 400);
	        }

	        if (empty($schoolId)) {
	            return response()->json(['error' => 'School ID is required'], 400);
	        }

	        $schoolCode = DB::table('schools')->where('id', $schoolId)->value('school_code');
	        
	        if (!$schoolCode) {
	            return response()->json(['error' => 'School reference not found'], 404);
	        }

	        $timestamp = date('d_Hi');
	        $getClassesBySkill = function ($skillId) {
	            if ($skillId >= 1 && $skillId <= 17) return [1, 2, 3];
	            if ($skillId == 18) return range(1, 12);
	            return range(4, 12);
	        };

	        $year = (int) date('Y');
	        $month = (int) date('m');
	        $academicYear = ($month >= 4)
	            ? $year . '-' . ($year + 1)
	            : ($year - 1) . '-' . $year;

	        // Single Skill Export (Direct Excel Download)
	        if (count($skillIds) === 1) {
	            $skillId = $skillIds[0];
	            $classIds = $getClassesBySkill($skillId);

	            $studentIds = DB::table('students')
					->where('school_code', $schoolCode)
					->where('academic_year', $academicYear)
					->whereIn('class_id', $classIds)
					->where('status', 'active')
					->where(function ($query) {
						$query->where('students.class_id', '<', 5)
							->orWhere(function ($q) {
								$q->where('students.class_id', '>=', 5)
									->where('students.is_pwd', '!=', '1');
							});
					})
					->pluck('id')
					->toArray();

	            $skillName = DB::table('skill_reports')
	                ->where('id', $skillId)
	                ->value('skill_name');

	            $skillName = ($skillName === '600 meter run/walk') ? '600 meter run' : $skillName;

	            $export = new TestScoreTemplete(
	                $studentIds,
	                $skillName,
	                $skillId,
	                $schoolId,
	                $status
	            );

	            $fileName = "{$schoolCode}_{$skillName}_{$timestamp}_test_score.xlsx";

	            return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName);
	        }

	        // Multiple Skills Export (Compiled into ZIP using in-memory binary generation)
	        $zipFileName = "skill_templates_{$timestamp}.zip";
	        $zipPath = storage_path("app/private/{$zipFileName}");
	        
	        // Ensure directory exists
	        if (!file_exists(dirname($zipPath))) {
	            mkdir(dirname($zipPath), 0775, true);
	        }

	        $zip = new \ZipArchive();
	        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
	            return response()->json(['error' => 'Could not create ZIP archive on server'], 500);
	        }

	        $studentCache = [];

	        foreach ($skillIds as $skillId) {
	            $classIds = $getClassesBySkill($skillId);
	            $classKey = implode('-', $classIds);

	            if (!isset($studentCache[$classKey])) {
	                $studentCache[$classKey] = DB::table('students')
	                    ->where('school_code', $schoolCode)
	                    ->where('academic_year', $academicYear)
	                    ->whereIn('class_id', $classIds)
	                    ->where('status', 'active')
	                    ->where(function ($query) {
						$query->where('students.class_id', '<', 5)
							->orWhere(function ($q) {
								$q->where('students.class_id', '>=', 5)
									->where('students.is_pwd', '!=', '1');
							});
						})
	                    ->pluck('id')
	                    ->toArray();
	            }

	            $studentIds = $studentCache[$classKey];

	            $skillName = DB::table('skill_reports')
	                ->where('id', $skillId)
	                ->value('skill_name');

	            $skillName = ($skillName === '600 meter run/walk') ? '600 meter run' : $skillName;

	            $export = new TestScoreTemplete(
	                $studentIds,
	                $skillName,
	                $skillId,
	                $schoolId,
	                $status
	            );

	            // Generate Excel binary data directly in memory (avoids temp file permission/path bugs)
	            $excelBinary = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

	            // Add binary content straight into the zip archive entry
	            $zip->addFromString(
	                "{$schoolCode}_{$skillName}_{$timestamp}_test_score.xlsx",
	                $excelBinary
	            );
	        }

	        $zip->close();

	        return response()->download($zipPath)->deleteFileAfterSend(true);

	    } catch (\Throwable $e) {
	        \Log::error("Test Template Export Error for School ID {$request->schoolId}: " . $e->getMessage(), [
	            'trace' => $e->getTraceAsString()
	        ]);
	        return response()->json(['error' => 'An error occurred while generating the export files. Please try again.'], 500);
	    }
	}
	
	public function importTestData(Request $request) {

		$userId = Auth::user()->id;

		$schoolId = DB::table('school_reference')
			->where('school_user_id', $userId)
			->where('status', 1)
			->value('school_id');

		$schoolCode = DB::table('schools')
			->where('id', $schoolId)
			->value('school_code');
			
		DB::table('test_import_status')->updateOrInsert(
	        ['school_id' => $schoolId],
	        ['status' => DB::raw('status')]
	    );

	    $statusRow = DB::table('test_import_status')->where('school_id', $schoolId)->first();

	    if ($statusRow->status === 'processing') {
	        $lastUpdated = Carbon::parse($statusRow->updated_at);
	        if ($lastUpdated->diffInMinutes(now()) <= 5) {
	        	return response()->json([
			    	'error' => 'error',
			        'icon' => 'info',
			        'title' => 'Import in Progress',
			        'summary' => 'An import is already in progress. Please wait before uploading again.'
			    ]);
	        }

	        DB::table('test_import_status')->where('school_id', $schoolId)->update(['status' => 'idle']);
	    }

	    DB::table('test_import_status')->where('school_id', $schoolId)->update([
	        'status' => 'processing',
	        'updated_at' => now()
	    ]);

		try {
			$validator = Validator::make($request->all(), [
				'test_score' => [
					'required',
					'file',
					'mimes:xls,xlsx',
					'max:3000',
					new ExcelTestHeaderValidation,
					function ($attribute, $value, $fail) use ($schoolCode) {
						$originalName = $value->getClientOriginalName();

						if (!str_starts_with($originalName, $schoolCode)) {
							$fail("Invalid file name. The file must start with your school code");
						}

						if (!preg_match('/_test_score\.xlsx$/', $originalName)) {
							$fail('Invalid file name. The file name must end with "_test_score.xlsx".');
						}
					}
				],
			]);

			if ($validator->fails()) {
				$errorContent = '<ul style="list-style-type:none">';
				$dynamicTitle = 'Validation Error';

				foreach ($validator->errors()->all() as $error) {
					$errorContent .= "<li>{$error}</li>";

					if (str_contains($error, 'file name')) {
						$dynamicTitle = 'Filename Mismatch';
					} elseif (str_contains($error, 'headers')) {
						$dynamicTitle = 'Template Format Error';
					} elseif (str_contains($error, 'size')) {
						$dynamicTitle = 'File Too Large';
					}
				}

				$errorContent .= '</ul>';

				return response()->json([
					'error' => true,
					'icon' => 'error',
					'title' => $dynamicTitle,
					'summary' => $errorContent
				]);
			}

			$file = $request->file('test_score');
			$action = $request->post('event', 'preview');

			$spreadsheet = IOFactory::load($file->getPathname());
			$sheet = $spreadsheet->getActiveSheet();

			$skillId = (int) trim($sheet->getCell('A1')->getValue());
			$skillName = DB::table('skill_reports')->where('id', $skillId)->value('skill_name');
			if (!$skillId) {
				return response()->json([
					'error' => true,
					'icon' => 'error',
					'title' => 'Invalid Template',
					'summary' => 'Skill ID is missing or invalid in the uploaded file.'
				]);
			}

			$highestRow = $sheet->getHighestRow();
			$studentIds = [];

			for ($row = 4; $row <= $highestRow; $row++) {
				$studentId = trim($sheet->getCell('A' . $row)->getValue());
				if ($studentId !== '') {
					$studentIds[] = $studentId;
				}
			}

			$studentIds = array_unique($studentIds);
			$totalStudents = count($studentIds);

			$termMasterId = TermMaster::where('school_id', $schoolId)
				->where('is_active', 1)
				->whereDate('term_start_date', '<=', today())
				->whereDate('term_end_date', '>=', today())
				->value('id');

			if ($action === 'preview') {
				return response()->json([
					'step' => 'preview',
					'total_students' => $totalStudents
				]);
			}

			if ($skillId >= 1 && $skillId <= 15) {
				$existingCount = DB::table('skillreport_skilltype_termtype_mapping')
					->where('school_id', $schoolId)
					->where('skill_report_id', $skillId)
					->where('term_master_id', $termMasterId)
					->whereIn('student_id', $studentIds)
					->distinct('student_id')
					->count();
			} else {
				$existingCount = DB::table('SeniorTestResults')
					->where('SchoolID', $schoolId)
					->where('TestTypeID', $skillId)
					->where('TermId', $termMasterId)
					->whereIn('StudentID', $studentIds)
					->count();
			}

			if ($action === 'import' && $existingCount > 0) {
				return response()->json([
					'step' => 'confirm_override',
					'total_students' => $totalStudents,
					'existing_students' => $existingCount
				]);
			}

			$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
			$extension = $file->getClientOriginalExtension();

			$filename = $originalName . '_' . date('His') . '.' . $extension;
			// $storedPath = $file->storeAs('import_tests', $filename);
			$storedPath = Storage::disk('azure')->putFileAs("import_tests/{$schoolId}", $file, $filename);

			$log = TestImportLog::create([
				'school_id' => $schoolId,
				'user_id' => $userId,
				'file_path' => $storedPath,
				'skill_report_id' => $skillId,
				'status' => 'queued',
				'message' => $skillName . ' Test data has been queued. Please wait for completion.',
				'is_active' => 'active',
				'started_at' => now(),
			]);

			if ($action) {
				// $importData = ($skillId >= 1 && $skillId <= 15)
				// 	? new ImportFMSTestData($schoolId, $action, $userId, $skillId, null, $log->id)
				// 	: new ImportFitnessTestData($schoolId, $action, $userId, $skillId, null, $log->id);

				// Excel::import($importData, $file);
				TestDataImportJob::dispatch($schoolId, $action, $userId, $skillId, $storedPath, $log->id)->onQueue('upload_test');

				return response()->json([
					'icon' => 'success',
					'title' => 'Import Queued',
					'summary' => $skillName . ' Score import queued successfully. Check View History for the upload status.'
				]);
			}

		} catch (\Throwable $e) {
			
			return response()->json([
				'error' => true,
				'icon' => 'error',
				'title' => 'Error',
				'summary' => 'Import failed. Please try again later. ' . $e->getMessage(),
			]);
		} finally {
	        DB::table('test_import_status')->where('school_id', $schoolId)->update(['status' => 'idle', 'updated_at' => now()]);
	    }
	}
	    
	public function downloadTestUploadedFile($logId) {

        $log = TestImportLog::findOrFail($logId);
	    $filePath = $log->file_path; 

	    if (!$filePath || !Storage::disk('azure')->exists($filePath)) {
	        abort(404, 'File not found.');
	    }

	    return Storage::disk('azure')->download( $filePath, basename($filePath),
	        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
	    );
    }

	public function downloadTestErrorFile($logId) {

	    $log = TestImportLog::findOrFail($logId);
	    $filePath = $log->error_file; 
		$skillId = $log->skill_report_id;

		$skillName = DB::table('skill_reports')
					->where('id', $skillId)
					->value('skill_name');
		if ($skillName == '600 meter run/walk') {
			$skillName = '600 meter run';
		}

	    $fileName = pathinfo($filePath, PATHINFO_FILENAME);

	    if (!$filePath || !Storage::disk('azure')->exists($filePath)) {
	        abort(404, 'File not found.');
	    }
	    $jsonData = json_decode(Storage::disk('azure')->get($filePath), true);

	    if (!is_array($jsonData)) {
	        abort(500, 'Invalid JSON data.');
	    }

	    return Excel::download(new ExportTestImproperData($jsonData, $skillId, $skillName), $fileName . '.xlsx');
	}

}
