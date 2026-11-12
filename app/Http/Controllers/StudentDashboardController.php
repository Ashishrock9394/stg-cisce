<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentDashboard;
use App\Models\ClassModel;
use App\Models\StudentInfo;
use App\Models\Sclass;
use App\Models\School;
use App\Models\Sstudent;
use App\Models\Food;
use App\Models\FoodItems;
use Carbon\Carbon;
use App\Models\WaterIntake;
use App\Models\Report;
use App\Models\Activity;
use App\Models\SleepRecord;
use App\Models\CalorieTarget;
use Illuminate\Support\Facades\DB;
use Response;
use Session;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\Helper;
use App\Models\TermMaster;
use App\Traits\ReportHelperTrait;
 
use Illuminate\Contracts\View\Factory as ViewFactory;

class StudentDashboardController extends Controller
{
	use ReportHelperTrait;
	
	function __construct()
	{
		$this->middleware('auth.auth_student')->except(['dashboard','ViewFitnessReport','getPerformance','getBmiBenchmark','getBenchmark','formatBenchmarks','formatRange','formatValue']);;
        
	}	

    public function dashboard_bk(Request $request){
        $title = "Student Dashboard";

        $year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

        if (Session::has('student_id')) {
            $student_id = Session::get('student_id');
        }else{
            $student_id = Auth::guard('sstudent')->user()->id;
        }

        $SchoolId = DB::table('students')->where('id', $student_id)->value('school_id');

        if ($request->filled('termSelect')) {
            // [$selectedYear, $selectedTermId] = explode('|', $request->termSelect);
            $selectedTermId = $this->getTermId($SchoolId);
            $selectedYear = $request->termSelect;
        } else {
            $selectedTermId = $this->getTermId($SchoolId);
            $selectedYear = $academicYear;
        }        

        $admissionNumber = DB::table('students')->where('id',$student_id)->value('student_uid');

        $studentId = DB::table('students')->where('school_id', (string) $SchoolId)->where('academic_year', $selectedYear)->where('student_uid', $admissionNumber)->value('id');

        if (!$studentId) {
            $studentId = $student_id;
            $selectedTermId = $this->getTermId($SchoolId);
        }

        // echo "<pre>"; print_r($studentId);exit();
           
        $studentData = DB::table('students')        
        ->join('schools', 'schools.id', '=' , 'students.school_id')
        ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->join('class', 'custom_classes.class_id', '=', 'class.id')
        ->select(
            'schools.school_name as school_name', 
            'schools.id as school_id', 
            'students.student_name', 
            'students.class_id',
            'students.custom_class_id',
            'students.gender', 
            'students.dob',
            'custom_classes.section',
            'students.rollno',
            'students.is_pwd',
            DB::raw("
                CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                END AS className
            ")
        )
        ->where('students.id', $studentId)
        ->first();

        $dob          = Carbon::parse($studentData->dob);
	    $studentAge   = $dob->age;
        
        $ageGender = $studentAge . strtolower(substr($studentData->gender, 0, 1));
        $classId = $studentData->class_id;

        $bmiRecord = DB::table('SeniorTestResults as str')
        ->join('term_masters as tm', 'tm.id', '=', 'str.TermId')
        ->select('str.height', 'str.weight', 'str.score', 'str.level')
        ->where('str.TestTypeID', 18)
        ->where('str.StudentID', $studentId)
        ->where('str.TermId', $selectedTermId)
        ->orderBy('str.ResultId', 'desc')
        ->limit(1)
        ->first();

        $getBmiBenchmark = $getBmiBenchmark =  $this->getBmiBenchmark($ageGender);
        
        // dd($bmiRecord);

        $class = DB::table('custom_classes')
		->join('class','class.id','=','custom_classes.class_id')
		->select('custom_classes.id','class_id','section',

			DB::raw("CASE 
                WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                THEN custom_classes.nomenclature 
                ELSE class.name 
            END AS classname")

			// 'class.name AS classname'
		)
		->where('class.id', $classId)
		->Where('school_id', $SchoolId)
		->orderBy('custom_classes.orders', 'ASC')
		->get();

        $fmsApplicable = DB::table('class_fitness_tests')
            ->where('class_id', $classId)
            ->pluck('test_type_id')
            ->toArray();

        $fmsTestData = DB::table('TestTypeMaster')
        ->join('skill_reports', 'skill_reports.TestTypeMasterID', '=', 'TestTypeMaster.TestTypeID')
        ->join('skillreport_skilltype_termtype_mapping as sst', function ($join) use ($studentId, $selectedTermId) {
            $join->on('sst.skill_report_id', '=', 'skill_reports.id')
                ->where('sst.student_id', $studentId)
                ->where('sst.term_master_id', $selectedTermId);
        })
        ->select(
            'TestTypeMaster.TestTypeID',
            'skill_reports.skill_name',
            'skill_reports.icons',
            DB::raw('COUNT(sst.id) as score')
        )
        ->whereIn('TestTypeMaster.TestTypeID', $fmsApplicable)
        ->groupBy(
            'TestTypeMaster.TestTypeID',
            'skill_reports.skill_name',
            'skill_reports.icons'
        )
        ->get();

        
        foreach ($fmsTestData as $item) {
            $count = $item->score;

            if ($count == 1) {
                $item->outcome = 'Emerging';
            } elseif ($count == 2) {
                $item->outcome = 'Developing';
            } elseif ($count == 3) {
                $item->outcome = 'Acquired';
            } elseif ($count == 4) {
                $item->outcome = 'Advanced';
            } elseif ($count == 5) {
                $item->outcome = 'Accomplished';
            } else {
                $item->outcome = 'Not Attempted';
            }
        }

        $fitnessTest = DB::table('SeniorTestResults as str')
            ->join('skill_reports', 'skill_reports.id', '=', 'str.TestTypeID')
            ->where('str.StudentID', $studentId)
            ->where('str.TermId', $selectedTermId)
            ->whereNotIn('str.TestTypeID', [18])
            ->get();

        $levelLabels = [
            'L0' => "Inadequate",
            'L1' => "Very Low",
            'L2' => "Low",
            'L3' => "Developing",
            'L4' => "Moderate",
            'L5' => "Good",
            'L6' => "High",
            'L7' => "Excellent",
            'L8' => "Extraordinary",
        ];

        foreach ($fitnessTest as $item) {
            $level = $item->level;
            $item->levelOutcome = $levelLabels[$level] ?? 'Unknown';
        }

        $categories_id2 = [];
        $categoty = "";

        if(in_array($studentData->class_id, [1, 2, 3])){
            $categories_id2 = [6,2];
        }else{
            $categories_id2 = [3,10,12,11,8,9,5,4,15];
        }
        if($studentData->gender == 'Male'){
            $categoty = "Boys";
        }else{
            $categoty = "Girls";
        }
        

        $getFitnessBenchmark = $this->getBenchmark($studentAge, $categoty, $categories_id2);

        $TermMasterId = $this->getTermId($SchoolId);
        
		$termIds = $this->getCurrentAndPreviousTermIds($SchoolId, (int) $TermMasterId);
        $terms = TermMaster::whereIn('id', $termIds)
			->get();

        
        return view('parent.student-dashboard', compact('title','studentId', 'getBmiBenchmark', 'studentData', 'studentAge', 'getFitnessBenchmark', 'bmiRecord','class','classId','fmsTestData','fitnessTest','selectedTermId','terms'));
    }

    public function dashboard(Request $request){
        $title = "Student Dashboard";

        $year = date('Y');
        $month = date('m');

        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

        [$startYear, $endYear] = explode('-', $academicYear);
        $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

        if (Session::has('student_id')) {
            $student_id = Session::get('student_id');
        } else {
            $student_id = Auth::guard('sstudent')->user()->id;
        }

        $student = DB::table('students')
            ->where('id', $student_id)
            ->select(
                'apaarId',
                'student_uid',
                'school_id'
            )
            ->first();

        if (!$student) {
            return redirect()->route('students.view.dashboard');
        }

        $SchoolId = $student->school_id;
        $apaarId  = $student->apaarId;

        if ($request->filled('termSelect')) {
            // [$selectedYear, $term_id] = explode('|', $request->termSelect);
            $selectedYear = $request->termSelect;
        } else {
            $selectedYear = $academicYear;
        }

        if (!empty($apaarId)) {
            $studentDetails = DB::table('students')
                ->where('academic_year', $selectedYear)
                ->where('apaarId', $apaarId)
                ->select('id', 'school_id', 'student_uid')->first();
            $studentId = $studentDetails->id; 
            $SchoolId = $studentDetails->school_id; 
        } else {
            $studentId = DB::table('students')
                ->where('school_id', (string) $SchoolId)
                ->where('academic_year', $selectedYear)
                ->where('student_uid', $student->student_uid)
                ->value('id');
        }

        if (!$studentId) {
            return redirect()->route('students.view.dashboard');
        }
        
        $selectedTermId = $this->getTermId($SchoolId, $selectedYear);

        $studentData = DB::table('students')
            ->join('schools', 'schools.id', '=', 'students.school_id')
            ->leftJoin(
                'custom_classes',
                'students.custom_class_id',
                '=',
                'custom_classes.id'
            )
            ->join(
                'class',
                'custom_classes.class_id',
                '=',
                'class.id'
            )
            ->select(
                'schools.school_name as school_name',
                'schools.id as school_id',
                'students.student_name',
                'students.class_id',
                'students.custom_class_id',
                'students.gender',
                'students.dob',
                'custom_classes.section',
                'students.rollno',
                'students.is_pwd',
                DB::raw("
                    CASE
                        WHEN custom_classes.nomenclature IS NOT NULL
                            AND custom_classes.nomenclature <> ''
                        THEN custom_classes.nomenclature
                        ELSE class.name
                    END AS className
                ")
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$studentData) {
            abort(404, 'Student details not found.');
        }

        $dob = Carbon::parse($studentData->dob);
        $studentAge = $dob->age;

        $ageGender = $studentAge .
            strtolower(substr($studentData->gender, 0, 1));

        $classId = $studentData->class_id;

        $bmiRecord = DB::table('SeniorTestResults as str')
            ->join(
                'term_masters as tm',
                'tm.id',
                '=',
                'str.TermId'
            )
            ->select(
                'str.height',
                'str.weight',
                'str.score',
                'str.level'
            )
            ->where('str.TestTypeID', 18)
            ->where('str.StudentID', $studentId)
            ->where('str.TermId', $selectedTermId)
            ->orderBy('str.ResultId', 'desc')
            ->first();

        $getBmiBenchmark = $this->getBmiBenchmark($ageGender);

        $class = DB::table('custom_classes')
            ->join(
                'class',
                'class.id',
                '=',
                'custom_classes.class_id'
            )
            ->select(
                'custom_classes.id',
                'class_id',
                'section',
                DB::raw("
                    CASE
                        WHEN custom_classes.nomenclature IS NOT NULL
                            AND custom_classes.nomenclature <> ''
                        THEN custom_classes.nomenclature
                        ELSE class.name
                    END AS classname
                ")
            )
            ->where('class.id', $classId)
            ->where('school_id', $SchoolId)
            ->orderBy('custom_classes.orders', 'ASC')
            ->get();

        $fmsApplicable = DB::table('class_fitness_tests')
            ->where('class_id', $classId)
            ->pluck('test_type_id')
            ->toArray();

        $fmsTestData = DB::table('TestTypeMaster')
            ->join(
                'skill_reports',
                'skill_reports.TestTypeMasterID',
                '=',
                'TestTypeMaster.TestTypeID'
            )
            ->join(
                'skillreport_skilltype_termtype_mapping as sst',
                function ($join) use ($studentId, $selectedTermId) {

                    $join->on(
                        'sst.skill_report_id',
                        '=',
                        'skill_reports.id'
                    )
                    ->where('sst.student_id', $studentId)
                    ->where('sst.term_master_id', $selectedTermId);
                }
            )
            ->select(
                'TestTypeMaster.TestTypeID',
                'skill_reports.skill_name',
                'skill_reports.icons',
                DB::raw('COUNT(sst.id) as score')
            )
            ->whereIn(
                'TestTypeMaster.TestTypeID',
                $fmsApplicable
            )
            ->groupBy(
                'TestTypeMaster.TestTypeID',
                'skill_reports.skill_name',
                'skill_reports.icons'
            )
            ->get();

        foreach ($fmsTestData as $item) {

            $item->outcome = match ((int) $item->score) {
                1 => 'Emerging',
                2 => 'Developing',
                3 => 'Acquired',
                4 => 'Advanced',
                5 => 'Accomplished',
                default => 'Not Attempted',
            };
        }

        $fitnessTest = DB::table('SeniorTestResults as str')
            ->join(
                'skill_reports',
                'skill_reports.id',
                '=',
                'str.TestTypeID'
            )
            ->where('str.StudentID', $studentId)
            ->where('str.TermId', $selectedTermId)
            ->whereNotIn('str.TestTypeID', [18])
            ->get();

        $levelLabels = [
            'L0' => 'Inadequate',
            'L1' => 'Very Low',
            'L2' => 'Low',
            'L3' => 'Developing',
            'L4' => 'Moderate',
            'L5' => 'Good',
            'L6' => 'High',
            'L7' => 'Excellent',
            'L8' => 'Extraordinary',
        ];

        foreach ($fitnessTest as $item) {
            $item->levelOutcome =
                $levelLabels[$item->level] ?? 'Unknown';
        }

        if (in_array($studentData->class_id, [1, 2, 3])) {
            $categories_id2 = [6, 2];
        } else {
            $categories_id2 = [
                3,
                10,
                12,
                11,
                8,
                9,
                5,
                4,
                15
            ];
        }

        $categoty = $studentData->gender == 'Male'
            ? 'Boys'
            : 'Girls';

        $getFitnessBenchmark = $this->getBenchmark(
            $studentAge,
            $categoty,
            $categories_id2
        );

        
        return view(
            'parent.student-dashboard',
            compact(
                'title',
                'studentId',
                'getBmiBenchmark',
                'studentData',
                'studentAge',
                'getFitnessBenchmark',
                'bmiRecord',
                'class',
                'classId',
                'fmsTestData',
                'fitnessTest',
                'selectedTermId',
                'academicYear',
                'previousAcademicYear',
                'selectedYear',
            )
        );
    }

}
