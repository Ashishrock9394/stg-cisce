<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sclass;
use App\Models\Teachingthrough;
use App\Models\Skill;
use App\Models\Sport;
use App\Models\Technique;
use App\Models\School;
use App\Models\Activity;
use App\Models\Report;
use App\Models\TrainerActivity;
use App\Models\TrainerOtherDuty;
use DB;
use Response;
use Validator;
use Redirect;
use paginate;
use App\Helpers\Helper;
use Session;
use Excel;
use App\Imports\TermExamImportData;
use App\Models\ViewDart;
use App\Models\TermMaster;
use Illuminate\Support\Facades\Log;
use Auth;
class FillDartController extends Controller
{
	
	public function __construct()  {
      
		$this->middleware(['auth','trainerschool']);
    }


	public function dashboard(Request $request)	{

		$user = auth()->user();
		$role_id =  \Auth::user()->role_id;
		if($role_id == 9){
			return redirect()->route('cicse.dashboard');
		}

		
		$userId  =  \Auth::id();
		$wrongDobStudents = collect();

		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

		if($role_id == 4){
			$school_id = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
			$SchoolCode = DB::table('schools')->select('school_code')->where('id', $school_id)->value('school_code');
			$wrongDobStudents = DB::table(DB::raw("(SELECT *, STR_TO_DATE(dob, '%Y-%m-%d') AS dob_date FROM students) as s"))
			->join('class', 'class.id', '=', 's.class_id')
			->selectRaw("
				s.id,
				s.school_code,
				s.student_uid,
				s.student_name AS name,
				s.class_id,
				s.section_id,
				s.rollno,
				s.dob,
				class.min_age,
				class.max_age,
				TIMESTAMPDIFF(YEAR, s.dob_date, CURDATE()) as age,

				CASE
					WHEN s.dob IS NULL OR s.dob = '' THEN 'invalid date'
					WHEN s.dob NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN 'invalid date'
					WHEN s.dob_date IS NULL THEN 'invalid date'
					WHEN s.dob_date > CURDATE() THEN 'future date'
					WHEN TIMESTAMPDIFF(YEAR, s.dob_date, CURDATE()) < (class.min_age - 1)
						THEN CONCAT('less than ', class.min_age - 1, ' years')

					WHEN TIMESTAMPDIFF(YEAR, s.dob_date, CURDATE()) > (class.max_age + 2)
						THEN CONCAT('greater than ', class.max_age + 2, ' years')
					ELSE NULL
				END AS issue
			")
			->where('s.school_code', $SchoolCode)
			->where('s.academic_year', $academicYear)
			->where('s.status', 'active')
			->havingRaw("issue IS NOT NULL")
			->orderBy('s.class_id', 'ASC')
			->get();
		}
		
		

		$title   = 'Dashboard';
		$SchoolName = '';
		
		$SchoolTrainers = DB::table('school_trainers')
		->join('schools','schools.id','=','school_trainers.school_id')
		->select('schools.school_name','schools.id','schools.logo')
		->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)

		//->where('schools.id', '!=', 3131)

		->get();
		
		$hasSchools = $SchoolTrainers->isNotEmpty();  // true if user has one or more schools


		if($hasSchools && count($SchoolTrainers) == 1) {
			// Automatically select the only school
			Session::put('SelectSchoolId', $SchoolTrainers[0]->id);
			$SchoolName = DB::table('schools')->select('school_name','logo')->where('id', $SchoolTrainers[0]->id)->first();
		}
			
		$select_school_id = $request->input('select_school_id');

		
		if($hasSchools &&  !empty($select_school_id)) {
		 	Session::put('SelectSchoolId', $select_school_id);	
		  	$SchoolName = DB::table('schools')->select('school_name','logo')->where('id', $select_school_id)->first();
		}
		
		if(Session::get('SelectSchoolId')) {


		  $SchoolName = DB::table('schools')->select('school_name','logo')->where('id', Session::get('SelectSchoolId'))->first();
		}
		
       	 // Log::info('hasSchools: ' . ($hasSchools ? 'true' : 'false'));

		
		$title = 'Dashboard';
		$notifications = auth()->user()->notifications()->latest()->take(12)->get();
		
		$dashboardModules = collect();

		switch ($role_id) {
			case 8: // School Role
				$schoolRef = DB::table('school_reference')
					->where('school_user_id', $userId)
					->first();
				
				$schoolDetails = null;
				if ($schoolRef) {
					$schoolDetails = DB::table('schools')
						->select('school_name', 'logo', 'school_code')
						->where('id', $schoolRef->school_id)
						->first();
				}

				if ($schoolRef && $schoolRef->status == 1) {
					$allowedModules = json_decode($user->usermeta->module_access ?? '[]', true);
					// dd($user->usermeta);
					$dashboardModules = DB::table('dashboard_modules')
						->whereIn('route_name', $allowedModules)
						// ->where('role_id', 4)
						->where('status', 1)
						->orderBy('order_no', 'asc')
						->get();
				}

				return view('fill-darts.dashboard', compact('title','SchoolTrainers','SchoolName', 'hasSchools','notifications','wrongDobStudents', 'dashboardModules'));
				break;

		    default:
		        break;
		}
		
		return view('fill-darts.dashboard', compact('title','SchoolTrainers','SchoolName', 'hasSchools','notifications','wrongDobStudents'));
	}

}
