<?php

namespace App\Http\Controllers;
ini_set('memory_limit','2048M');

use DB;
use PDF;
use Auth;
use Artisan;
use Session;
use Redirect;
use DateTime;
use paginate;
use Response;
use Validator;
use DataTables;
use ZipArchive;

use Dompdf\Dompdf;
use Carbon\Carbon;
use App\Helpers\Helper;

use App\Models\User;
use App\Models\Sport; 
use App\Models\Board;
use App\Models\State;
use App\Models\Region;
use App\Models\School;
use App\Models\Sclass;
use App\Models\Report;
use App\Models\Teacher;
use App\Models\Usermeta;
use App\Models\Activity;
use App\Models\Sstudent;
use App\Models\ViewDart;
use App\Models\District;
use App\Models\TermMaster;
use App\Models\ScustomClass;
use App\Models\SchoolTrainer;

use App\Helpers\AuditHelper;

use App\Models\Teachingthrough;
use App\Models\StudentImportLog;
use App\Models\SkillReportSkillTypeTermTypeMapping;
use App\Models\DashboardModule;

use App\Imports\ImportIcseStudents;
use App\Imports\ImportStudentProfile;
use App\Exports\ExportImproperData;
use App\Exports\TrainerCredentials;
use App\Exports\StudentsCredentialsExport;
use App\Exports\ExportStudentProfile;
use App\Exports\SchoolAdminCredentials;
use App\Exports\StudentEmailTemplete;
use App\Exports\StudentEmailErrorExport;
use App\Jobs\ImportStudentEmailJob;

use App\Jobs\ProcessStudentImport;
use App\Jobs\PromoteStudentsJob;
use App\Jobs\PromoteStudentsByIdsJob;
use App\Rules\ExcelHeaderValidation;
use App\Contracts\EmailServiceInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Crypt;
use App\Traits\ReportHelperTrait;

class SchoolRecordController extends Controller
{
	use ReportHelperTrait;

	public function __construct() {
		
        $this->middleware(['auth:web','restrict.access'])->except(['ViewFitnessReport','getPerformance','getBmiBenchmark']);
    }

    public function downloadInvalidData(){

    	$filename = Session::get('invalid_rows_file');
        if (!$filename || !Storage::disk('local')->exists($filename)) {
	        abort(404, 'No failed import records found.');
	    }

        $failedRecords = json_decode(Storage::disk('local')->get($filename), true);
        return Excel::download(new ExportImproperData($failedRecords,'error_list'), 'ErrorList_'.date("d-m-Y H:i:s").'.xlsx')->deleteFileAfterSend(false);
    }

    public function SchoolDashboard() {

    	$userId  = Auth::user()->id;
		$role_id =  \Auth::user()->role_id;

		switch ($role_id) {
	        case 9:
	            return redirect()->route('cicse.dashboard');
	        case 4:
	            return redirect()->route('filldart.dashboard');
	        default:
	            return redirect()->back()->with('error', 'Unauthorized access.');
	    }
    }

    /**
     * 05-08-2024
     * Method used to get all the school's student details.
     * */

    public function ManageStudents_bk(Request $request){
   
    	$userId = Auth::user()->id;

		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		$data = ScustomClass::where('school_id', $schoolId)->select('class_id','section')->get()->toArray();
		$customClass1 = array();

		foreach($data as $value){

			$class = $value['class_id'];
			$section = $value['section'];

			if(!isset($customClass1[$class])){
				$customClass1[$class] = [];
			}

			$customClass1[$class][] = $section;
		}


		$school = School::find($schoolId);
		$classList = $school->getClasses;
		foreach ($classList as $class) {
		   // $class->name = Sclass::where('id', $class->class_id)->orderBy('orders')->first()->name;

		    $originalClass = Sclass::where('id', $class->class_id)->orderBy('orders')->first();
		    $class->name = !empty($class->nomenclature) 
		        ? $class->nomenclature 
		        : ($originalClass ? $originalClass->name : null);

		}


		$classList->prepend((object)[
	        'class_id' => '',
	        'name' => 'Select Class',
	        'section' => ''
	    ]);


		$year = date('Y');
        $month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

			[$startYear, $endYear] = explode('-', $academicYear);
			$previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

		if ($request->filled('academic_year')) {
			$selectedYear = $request->academic_year;
		} else {

			$hasPreviousYearData = DB::table('students')
				->where('school_code', $school->school_code)
				->where('academic_year', $previousAcademicYear)
				->exists();
			// Note: do not delete
			// $selectedYear = $hasPreviousYearData
			// 	? $previousAcademicYear
			// 	: $academicYear;

			$selectedYear = $academicYear;
		}

		$classes = DB::table('schools')
			->select('class.id','class.name as className', 'nomenclature')
			->join('custom_classes' ,'custom_classes.school_id' ,'=' ,'schools.id')
			->join('class','class.id','=','custom_classes.class_id')
			->where('schools.id' ,$schoolId )
			->where('class.status' , 1 )
			->groupBy('class.id','class.name','nomenclature')
			->orderBY('class.orders')
			->get();

		//echo "<pre>"; print_r($classes);exit();

		$studentsQuery = DB::table('schools')
		->join('students', 'students.school_id', '=' , 'schools.id')
		->leftJoin('student_rpwd_mapping', 'student_rpwd_mapping.student_id', '=', 'students.id')
		->leftJoin('class', 'students.class_id', '=', 'class.id')
    	->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->select(
			'schools.id as schools_id',
			'schools.school_code as school_code',
			'students.id as student_id',
			'students.student_uid as admissionnumber',
			'students.student_name as student_name',
			'students.gender',
			'students.class_id',
			'students.section_id',
			'students.custom_class_id',
			'students.dob',
			'students.email_id',
			'students.rollno',
			'students.status',
			'students.is_pwd',
			'students.paymentStatus',
			'student_rpwd_mapping.pwd_type_id',
			'student_rpwd_mapping.anthropo_ht_id',
			'student_rpwd_mapping.anthropo_wt_id',
			'students.created_at',
			'students.academic_year',
			'students.email_flag',
			DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                 END AS display_classname"),
        	'custom_classes.section',

        	DB::raw("TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) AS age"),
		
			DB::raw("
		        CASE 
		            WHEN 
		                (
		                    (students.class_id = 1 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 4 AND 9) OR
		                    (students.class_id = 2 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 5 AND 10) OR
		                    (students.class_id = 3 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 6 AND 11) OR
		                    (students.class_id = 4 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 7 AND 12) OR
		                    (students.class_id = 5 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 8 AND 13) OR
		                    (students.class_id = 6 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 9 AND 14) OR
		                    (students.class_id = 7 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 10 AND 15) OR
		                    (students.class_id = 8 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 11 AND 16) OR
		                    (students.class_id = 9 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 12 AND 17) OR
		                    (students.class_id = 10 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 13 AND 18) OR
		                    (students.class_id = 11 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 14 AND 19) OR
		                    (students.class_id = 12 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 15 AND 20)
		                )
		            THEN 1
		            ELSE 0
		        END AS isValidAge
		    ")
		)
		->orderBy('students.class_id')
		->orderBy('students.section_id')
		->orderBy('students.rollno')
		->where('students.school_code', $school->school_code);

		$emailGroups = (clone $studentsQuery)
		->where('students.academic_year', $academicYear)
		->where('students.status', 'active')
		->where('students.email_flag', '0')
		->get()
		->groupBy('email_id');

		$studentsDetails1 = (clone $studentsQuery)->get();
		$emailCounts = $studentsDetails1->groupBy('email_id')->map->count();

		if ($request->filled('academic_year')) {
			$studentsQuery->where('students.academic_year', $request->academic_year);
		} else {
			$studentsQuery->where('students.academic_year', $selectedYear);
		}
		
		if ($request->filled('class_id')) {
			$studentsQuery->where('students.class_id', $request->class_id);
		}

		if ($request->filled('section_id')) {
			$studentsQuery->where('students.section_id', $request->section_id);
		}

        if ($request->filled('status')) {
			$status = $request->input('status');
			if ($status === 'all') {
				$studentsQuery->whereIn('students.status', ['active', 'transfer', 'promoted', 'failed',]);
			}else if($status === 'paid'){
				$studentsQuery->where('students.paymentStatus', $status);
			}else if($status === 'unpaid'){
				$studentsQuery->whereIn('students.paymentStatus', ['pending', 'unpaid']);
			}else{
				$studentsQuery->where('students.status', $status);			
			}
	    }
		// for physically disabled
		// if ($request->filled('is_pwd')) {
		// 	$studentsQuery->where('students.is_pwd', $request->input('is_pwd'));
		// }

		$studentsDetails  = (clone $studentsQuery)->get();

    	if($request->ajax()){

			return Datatables::of($studentsDetails)
	        ->addIndexColumn()

			->addColumn('checkbox', function($row) use ($academicYear){
				if($row->academic_year != $academicYear && $row->status != 'active') {
					return '<input type="checkbox" class="row-select abc" value="'.$row->student_id.'" data-id=" '.$row->student_id .'" disabled>';
				}else{
					return '<input type="checkbox" class="row-select" value="'.$row->student_id.'" data-id="'. $row->student_id .'">';
				}
		    })

	        ->addColumn('class_id', function($row) {
	        	return $row->display_classname;
            })

			->addColumn('section_id', function($row) use ($customClass1, $academicYear){
				if ($row->academic_year == $academicYear){
					$html = '<select class="form-control mx-0 w-100" name="section_id" data-section="'.$row->section_id.'" data-id= '.$row->student_id.' id="section" value="'.$row->class_id.'" >
						<option value="">Section</option>';
						foreach ($customClass1[$row->class_id] as $section) {
							$html .= '<option value="' . $row->class_id . '"';
							if ($row->section_id == $section) {
								$html .= ' selected';
							}
							$html .= '>' . $section . '</option>';
						}
					$html .= '</select>';
					return $html;
				}else{ 	
					return $row->section_id;
				}       	
            })

            ->addColumn('gender' , function($row) {
				return $row->gender;
            })

	        ->addColumn('dob', function($row) {

	        	$formatted_date = null;
		        if (!empty($row->dob)) {
		            $timestamp = strtotime($row->dob);
		            if ($timestamp !== false) {
		                $formatted_date = date('Y-m-d', $timestamp);
		            } else {
		                $formatted_date = 'Fill date';
		            }
		        } else {
		            $formatted_date = 'No date provided';
		        }

		   		$datehtml = '';
				$hasDobError = false;

				if ((isset($row->isValidAge) && $row->isValidAge === 0) || $formatted_date == 'Fill date') {
					$hasDobError = true;
				}
				$formattedDob = date('d M Y', strtotime($row->dob));

				if ($hasDobError) {
					return '<span class="has-dob-error"
						data-dob-error="true"
						data-dob="'.$formattedDob.'"
						data-id="'.$row->student_id.'">'
						.$formattedDob.
					'</span>';
				} else {
					$datehtml .= '<span
						data-dob="'.$formattedDob.'"
						data-id="'.$row->student_id.'">'
						.$formattedDob.
					'</span>';
				}

                return $datehtml;
            })

			->addColumn('email_id', function ($row) use ($emailCounts) {
				$count = $emailCounts[$row->email_id] ?? 0;
				$email = ($row->email_id);
				// if ($count >= 2) {
				// 	$email .= ' <span class="badge badge-danger"><i class="fa fa-ban" title="This email id is used '  . $count . ' times"></i></span>';
				// }
				return $email;
			})

	        ->addColumn('status', function($row) use ($academicYear){
				$status = null;
				if ($row->academic_year == $academicYear){
					$status = ['active','transfer'];				

				}else{
					$status = ['active','transfer','promoted','failed'];
				}
	        	
        		$statusHtml = '<select class="form-control" name="status" id="studentStatus" data-status="'.$row->status.'" data-id="'.$row->student_id.'" >
	                <!--option value="">Select Status</option-->';
					foreach ($status as $data) {
						$statusHtml .= '<option value="' . $data . '"';
						if ($data == $row->status) {
							$statusHtml .= ' selected';
						}
						$statusHtml .= '>' . ucfirst($data) . '</option>';
					}
				$statusHtml .= '</select>';
            	
            	return $statusHtml;
				// return $row->status;


            })
			->addColumn('student_display_name', function($row) {
				if($row->is_pwd == 1){
					return $row->student_name . ' <span style="color:white; background:#e74c3c; padding:2px 6px; border-radius:4px; font-size:10px;">CWSN</span>';
				} else {
					return $row->student_name;
				}
			})
			->addColumn('created_at', function($row) {
				return Carbon::parse($row->created_at)->format('d-m-Y');
			})

			->addColumn('rollno', function($row) use($academicYear) {
				if ($row->academic_year == $academicYear){
					return '<input type="text" class="form-control rollno-input" data-rollno="'.$row->rollno.'"  value="'.$row->rollno.'" data-id="'.$row->student_id.'">';
				}else{
					return $row->rollno;
				}
			})
			->addColumn('paymentStatus', function($row) use($academicYear) {
				if ($row->paymentStatus == 'paid'){
					return 'Paid';
				}else{
					return 'Unpaid';
				}
			})

			->addColumn('edit_button', function($row) use($academicYear) {

				if ($row->academic_year == $academicYear){
					return '<button class="btn btn-sm btn-primary edit-student" 
									data-id="'.$row->student_id.'" 
									title="edit '.$row->student_name.' details">
								<i class="fas fa-edit"></i>
							</button>';
				}else{
					return '<button class="btn btn-sm btn-secondary" disabled>
								<i class="fas fa-edit"></i>
							</button>';
				}
			})
			

            ->rawColumns(['checkbox','student_display_name','gender','class_id','section_id','rollno','dob','email_id','status','created_at','edit_button'])
	        ->toJson();
        }


        if($studentsDetails->count() > 0){
        	$check  = 'true';
        }else{
        	$check = 'false';
        	$classList = Sclass::select('id','name')->where('status', 1)->orderBy('orders')->get();
        }

		$pwdDetails = DB::table('pwd_categories')
		->join('pwd_types', 'pwd_types.pwd_cat_id', '=', 'pwd_categories.id')->get();

		$anthropometricData = DB::table('anthropometric_table')->orderBy('id')->get()->groupBy('anthropometric_type');
	
        $logs = \App\Models\StudentImportLog::with('user')
        ->where('user_id', Auth::id())->where('is_active', 'active')
        ->orderBy('created_at', 'desc')
        ->get();
		$promotionLog = DB::table('students_promotion_status')->where('school_id',$schoolId)
		->orderBy('created_at', 'desc')
        ->get();

		$title = 'Manage Students';
		return view('school.managestudent', compact('title','studentsDetails','studentsDetails1','classes','check','classList','logs', 'promotionLog', 'data','pwdDetails','emailGroups','hasPreviousYearData', 'anthropometricData'));
	}  
	public function ManageStudents(Request $request){
   
    	$userId = Auth::user()->id;

		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		$data = ScustomClass::where('school_id', $schoolId)->select('class_id','section')->get()->toArray();
		$customClass1 = array();

		foreach($data as $value){

			$class = $value['class_id'];
			$section = $value['section'];

			if(!isset($customClass1[$class])){
				$customClass1[$class] = [];
			}

			$customClass1[$class][] = $section;
		}


		$school = School::find($schoolId);
		$classList = $school->getClasses;
		foreach ($classList as $class) {
		   // $class->name = Sclass::where('id', $class->class_id)->orderBy('orders')->first()->name;

		    $originalClass = Sclass::where('id', $class->class_id)->orderBy('orders')->first();
		    $class->name = !empty($class->nomenclature) 
		        ? $class->nomenclature 
		        : ($originalClass ? $originalClass->name : null);

		}


		$classList->prepend((object)[
	        'class_id' => '',
	        'name' => 'Select Class',
	        'section' => ''
	    ]);


		$year = date('Y');
        $month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

			[$startYear, $endYear] = explode('-', $academicYear);
			$previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

		if ($request->filled('academic_year')) {
			$selectedYear = $request->academic_year;
		} else {

			$hasPreviousYearData = DB::table('students')
				->where('school_code', $school->school_code)
				->where('academic_year', $previousAcademicYear)
				->exists();
				
			// $selectedYear = $hasPreviousYearData
			// 	? $previousAcademicYear
			// 	: $academicYear;

			$selectedYear = $academicYear;
		}

		$classes = DB::table('schools')
			->select('class.id','class.name as className', 'nomenclature')
			->join('custom_classes' ,'custom_classes.school_id' ,'=' ,'schools.id')
			->join('class','class.id','=','custom_classes.class_id')
			->where('schools.id' ,$schoolId )
			->where('class.status' , 1 )
			->groupBy('class.id','class.name','nomenclature')
			->orderBY('class.orders')
			->get();

		//echo "<pre>"; print_r($classes);exit();

		$studentsQuery = DB::table('schools')
		->join('students', 'students.school_id', '=' , 'schools.id')
		->leftJoin('student_rpwd_mapping', 'student_rpwd_mapping.student_id', '=', 'students.id')
		->leftJoin('class', 'students.class_id', '=', 'class.id')
    	->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->select(
			'schools.id as schools_id',
			'schools.school_code as school_code',
			'students.id as student_id',
			'students.student_uid as admissionnumber',
			'students.student_name as student_name',
			'students.gender',
			'students.class_id',
			'students.section_id',
			'students.custom_class_id',
			'students.dob',
			'students.email_id',
			'students.rollno',
			'students.status',
			'students.is_pwd',
			'students.paymentStatus',
			'student_rpwd_mapping.pwd_type_id',
			'student_rpwd_mapping.anthropo_ht_id',
			'student_rpwd_mapping.anthropo_wt_id',
			'students.created_at',
			'students.academic_year',
			'students.email_flag',
			DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                 END AS display_classname"),
        	'custom_classes.section',

        	DB::raw("TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) AS age"),
		
			DB::raw("
		        CASE 
		            WHEN 
		                (
		                    (students.class_id = 1 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 4 AND 9) OR
		                    (students.class_id = 2 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 5 AND 10) OR
		                    (students.class_id = 3 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 6 AND 11) OR
		                    (students.class_id = 4 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 7 AND 12) OR
		                    (students.class_id = 5 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 8 AND 13) OR
		                    (students.class_id = 6 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 9 AND 14) OR
		                    (students.class_id = 7 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 10 AND 15) OR
		                    (students.class_id = 8 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 11 AND 16) OR
		                    (students.class_id = 9 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 12 AND 17) OR
		                    (students.class_id = 10 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 13 AND 18) OR
		                    (students.class_id = 11 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 14 AND 19) OR
		                    (students.class_id = 12 AND TIMESTAMPDIFF(YEAR, students.dob, CURDATE()) BETWEEN 15 AND 20)
		                )
		            THEN 1
		            ELSE 0
		        END AS isValidAge
		    ")
		)
		->orderBy('students.class_id')
		->orderBy('students.section_id')
		->orderBy('students.rollno')
		->where('students.school_code', $school->school_code);

		$emailGroups = (clone $studentsQuery)
		->where('students.academic_year', $academicYear)
		->where('students.status', 'active')
		->where('students.email_flag', '0')
		->get()
		->groupBy('email_id');

		$studentsDetails1 = (clone $studentsQuery)->get();
		$emailCounts = $studentsDetails1->groupBy('email_id')->map->count();

		if ($request->filled('academic_year')) {
			$studentsQuery->where('students.academic_year', $request->academic_year);
		} else {
			$studentsQuery->where('students.academic_year', $selectedYear);
		}
		
		if ($request->filled('class_id')) {
			$studentsQuery->where('students.class_id', $request->class_id);
		}

		if ($request->filled('section_id')) {
			$studentsQuery->where('students.section_id', $request->section_id);
		}

        if ($request->filled('status')) {
			$status = $request->input('status');
			if ($status === 'all') {
				$studentsQuery->whereIn('students.status', ['active', 'transfer', 'promoted', 'failed',]);
			}else if($status === 'paid'){
				$studentsQuery->where('students.paymentStatus', $status);
			}else if($status === 'unpaid'){
				$studentsQuery->whereIn('students.paymentStatus', ['pending', 'unpaid']);
			}else{
				$studentsQuery->where('students.status', $status);			
			}
	    }
		// for physically disabled
		// if ($request->filled('is_pwd')) {
		// 	$studentsQuery->where('students.is_pwd', $request->input('is_pwd'));
		// }

		$studentsDetails  = (clone $studentsQuery)->get();

    	if($request->ajax()){

			return Datatables::of($studentsDetails)
	        ->addIndexColumn()

			->addColumn('checkbox', function($row) use ($academicYear){
				if($row->academic_year != $academicYear && $row->status != 'active') {
					return '<input type="checkbox" class="row-select abc" value="'.$row->student_id.'" data-id=" '.$row->student_id .'" disabled>';
				}else{
					return '<input type="checkbox" class="row-select" value="'.$row->student_id.'" data-id="'. $row->student_id .'">';
				}
		    })

	        ->addColumn('class_id', function($row) {
	        	return $row->display_classname;
            })

			->addColumn('section_id', function($row) use ($customClass1, $academicYear){
				if ($row->academic_year == $academicYear){
					$html = '<select class="form-control mx-0 w-100" name="section_id" data-section="'.$row->section_id.'" data-id= '.$row->student_id.' id="section" value="'.$row->class_id.'" >
						<option value="">Section</option>';
						foreach ($customClass1[$row->class_id] as $section) {
							$html .= '<option value="' . $row->class_id . '"';
							if ($row->section_id == $section) {
								$html .= ' selected';
							}
							$html .= '>' . $section . '</option>';
						}
					$html .= '</select>';
					return $html;
				}else{ 	
					return $row->section_id;
				}       	
            })

            ->addColumn('gender' , function($row) {
				return $row->gender;
            })

	        ->addColumn('dob', function($row) {

	        	$formatted_date = null;
		        if (!empty($row->dob)) {
		            $timestamp = strtotime($row->dob);
		            if ($timestamp !== false) {
		                $formatted_date = date('Y-m-d', $timestamp);
		            } else {
		                $formatted_date = 'Fill date';
		            }
		        } else {
		            $formatted_date = 'No date provided';
		        }

		   		$datehtml = '';
				$hasDobError = false;

				if ((isset($row->isValidAge) && $row->isValidAge === 0) || $formatted_date == 'Fill date') {
					$hasDobError = true;
				}
				$formattedDob = date('d M Y', strtotime($row->dob));

				if ($hasDobError) {
					return '<span class="has-dob-error"
						data-dob-error="true"
						data-dob="'.$formattedDob.'"
						data-id="'.$row->student_id.'">'
						.$formattedDob.
					'</span>';
				} else {
					$datehtml .= '<span
						data-dob="'.$formattedDob.'"
						data-id="'.$row->student_id.'">'
						.$formattedDob.
					'</span>';
				}

                return $datehtml;
            })

			->addColumn('email_id', function ($row) use ($emailCounts) {
				$count = $emailCounts[$row->email_id] ?? 0;
				$email = ($row->email_id);
				// if ($count >= 2) {
				// 	$email .= ' <span class="badge badge-danger"><i class="fa fa-ban" title="This email id is used '  . $count . ' times"></i></span>';
				// }
				return $email;
			})

	        ->addColumn('status', function($row) use ($academicYear){
				$status = null;
				if ($row->academic_year == $academicYear){
					$status = ['active','transfer'];				

				}else{
					$status = ['active','transfer','promoted','failed'];
				}
	        	
        		$statusHtml = '<select class="form-control" name="status" id="studentStatus" data-status="'.$row->status.'" data-id="'.$row->student_id.'" >
	                <!--option value="">Select Status</option-->';
					foreach ($status as $data) {
						$statusHtml .= '<option value="' . $data . '"';
						if ($data == $row->status) {
							$statusHtml .= ' selected';
						}
						$statusHtml .= '>' . ucfirst($data) . '</option>';
					}
				$statusHtml .= '</select>';
            	
            	return $statusHtml;
				// return $row->status;


            })
			->addColumn('student_display_name', function($row) {
				if($row->is_pwd == 1){
					return $row->student_name . ' <span style="color:white; background:#e74c3c; padding:2px 6px; border-radius:4px; font-size:10px;">CWSN</span>';
				} else {
					return $row->student_name;
				}
			})
			->addColumn('created_at', function($row) {
				return Carbon::parse($row->created_at)->format('d-m-Y');
			})

			->addColumn('rollno', function($row) use($academicYear) {
				if ($row->academic_year == $academicYear){
					return '<input type="text" class="form-control rollno-input" data-rollno="'.$row->rollno.'"  value="'.$row->rollno.'" data-id="'.$row->student_id.'">';
				}else{
					return $row->rollno;
				}
			})
			->addColumn('paymentStatus', function($row) use($academicYear) {
				if ($row->paymentStatus == 'paid'){
					return 'Paid';
				}else{
					return 'Unpaid';
				}
			})

			->addColumn('edit_button', function($row) use($academicYear) {

				if ($row->academic_year == $academicYear){
					return '<button class="btn btn-sm btn-primary edit-student" 
									data-id="'.$row->student_id.'" 
									title="edit '.$row->student_name.' details">
								<i class="fas fa-edit"></i>
							</button>';
				}else{
					return '<button class="btn btn-sm btn-secondary" disabled>
								<i class="fas fa-edit"></i>
							</button>';
				}
			})
			

            ->rawColumns(['checkbox','student_display_name','gender','class_id','section_id','rollno','dob','email_id','status','created_at','edit_button'])
	        ->toJson();
        }


        if($studentsDetails->count() > 0){
        	$check  = 'true';
        }else{
        	$check = 'false';
        	$classList = Sclass::select('id','name')->where('status', 1)->orderBy('orders')->get();
        }

		$pwdDetails = DB::table('pwd_categories')
		->join('pwd_types', 'pwd_types.pwd_cat_id', '=', 'pwd_categories.id')->get();

		$anthropometricData = DB::table('anthropometric_table')->orderBy('id')->get()->groupBy('anthropometric_type');
	
        $logs = \App\Models\StudentImportLog::with('user')
        ->where('user_id', Auth::id())->where('is_active', 'active')
        ->orderBy('created_at', 'desc')
        ->get();
		$promotionLog = DB::table('students_promotion_status')->where('school_id',$schoolId)
		->orderBy('created_at', 'desc')
        ->get();

		$title = 'Manage Students';
		return view('school.managestudent', compact('title','studentsDetails','studentsDetails1','classes','check','classList','logs', 'promotionLog', 'data','pwdDetails','emailGroups','hasPreviousYearData', 'anthropometricData'));
	} 

	/**
	 * Delete Students
	 * */
	public function DeleteStudent(Request $request) {

	    $action = $request->input('action');
	    $ids = $request->input('ids', []);

	    if (empty($ids) || !in_array($action, ['delete', 'trash'])) {
	        return response()->json(['message' => 'Invalid request.'], 400);
	    }
		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

		if ($action === 'delete') {
			
			
		    $students = DB::table('students')->select('id', 'school_id', 'school_code', 'student_uid', 'student_name', 'gender', 'class_id', 'custom_class_id', 'section_id','dob','user_id','email_id','rollno','academic_year','is_pwd','batchId','paymentStatus','batchCreatedAt','careersPaymentRef','gatewayTxnId','paidOn','updatedOn','apaarId')
		        ->whereIn('id', $ids)->where('academic_year', $academicYear)->get();

		    $deletedData = [];
		    $now = now(); 
		    $userId = Auth::id();

		    foreach ($students as $student) {
		        $deletedData[] = [
		            'student_id'       => $student->id,
		            'name'             => $student->student_name,
		            'deleted_by'       => $userId,
		            'deleted_at'       => $now,
		            'school_id'        => $student->school_id,
		            'custom_class_id'  => $student->custom_class_id,
		            'student_uid'      => $student->student_uid,
		            'json_data'        => json_encode((array) $student),
		        ];
		    }

		    if (!empty($deletedData)) {
		        DB::table('deleted_students')->insert($deletedData);
		    }

		    $deletedCount = DB::table('students')->whereIn('id', $ids)->where('academic_year', $academicYear)->delete();
		    AuditHelper::log('action', 'Deleted Students');

		    return response()->json([
				'status' => true,
				'deleted' => $deletedCount,
				'message' => "$deletedCount student(s) deleted successfully."
			]);
		}



	    if ($action === 'trash') {
	        Sstudent::whereIn('id', $ids)->update(['status' => 'trashed']);


	        return response()->json(['message' => 'Selected students moved to trash successfully.']);
	    }

	    return response()->json(['message' => 'Action performed successfully.']);
	}
	
	/**
   	 * Promote students by students Id 22_Apr
   	 * */
	
	public function PromoteStudentIds(Request $request){

		try {	

			$userId = Auth::id();

			$ids = $request->input('ids', []);
			
			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;

			[$startYear, $endYear] = explode('-', $academicYear);
			$previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);
				
			$studentIds = Sstudent::whereIn('id', $ids)
				->where('status', 'active')
				->where('academic_year', $previousAcademicYear)
				->pluck('id')
				->toArray();
			

			$schoolId = DB::table('school_reference')
					->where('school_user_id', $userId)
					->where('status', 1)
					->value('school_id');

			if (empty($studentIds)) {
				return response()->json([
					'status' => false,
					'message' => 'Selected students have already been promoted.'
				], 400);
			}

			$promotionId = DB::table('students_promotion_status')->insertGetId([
				'school_id' => $schoolId,
				'total_students' => count($studentIds),
				'promoted_students' => 0,
				'transferred_students' => 0,
				'status' => 'pending'
			]);

			PromoteStudentsByIdsJob::dispatch($studentIds, $schoolId, $promotionId)->onQueue('upload_test');

			return response()->json([
				'status' => true,
				'message' => 'Promotion Request Submitted Successfully.'
			]);
		} catch (\Throwable $th) {
			$promotionId = DB::table('students_promotion_status')->where('school_id', $schoolId,)->update([				
				'status' => 'failed'
			]);
			return response()->json([
				'status' => false,
				'message' => 'Failed',
				'error' => 'Promotion req failed. Please try again later'
			], 500);
		}
	}

	public function PromotionIdsStatus(){
		$userId = Auth::id();
		$schoolId = DB::table('school_reference')
			->where('school_user_id', $userId)
			->where('status', 1)
			->value('school_id');

		$logs = DB::table('students_promotion_status as sps')
			->select(
				'sps.total_students',
				'sps.promoted_students',
				'sps.transferred_students',
				'sps.status',
				'sps.updated_at',
				'sps.completed_at',
			)
			->where('sps.school_id', $schoolId)
			->orderByDesc('sps.id')
			->get();

		return response()->json([
			'html' => view('modals.students-promotion-status-table', compact('logs'))->render()
		]);
	}


	/**
   	 * Upadte DOB of the student
   	 * */

   	public function upadatedob(Request $request){
		try {

			$studentId = $request->post('student_id');
			$newDate = $request->post('new_date');

			$student = DB::table('students')->where('id', $studentId)->first();

			$age = \Carbon\Carbon::parse($newDate)->age;

			$isValidAge = $this->isValidAge($student->class_id, $age);

			// dd($request->has('force_update'));

			if (!$isValidAge && !$request->boolean('force_update')) {

				$classDetails = DB::table('class')
					->where('id', $student->class_id)
					->first();

				return response()->json([
					'status' => 'age_warning',
					'message' => "Age should be between {$classDetails->min_age} to {$classDetails->max_age} years for Class-{$student->class_id}. Do you want to proceed?"
				]);
			}

			// Update DOB
			DB::table('students')
				->where('id', $studentId)
				->update([
					'dob' => $newDate,
					'updated_at' => now(),
					]);

			return response()->json([
				'status' => 'success',
				'message' => 'DOB updated successfully.'
			]);

		} catch (\Exception $e) {

			return response()->json([
				'status' => 'fail',
				'message' => 'Something went wrong. Please try again.'
			]);
		}
	}

	// update roll number 

	public function updateRollno(Request $request){

		try {
			$validator = Validator::make($request->all(), [
				'rollNo' => ['required', 'alpha_num'],
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'fail',
					'message' => 'Roll number must be between 1 to 6 digits.',
					'errors' => $validator->errors()
				], 422);
			}

			$studentId = $request->student_id;
			$rollNo = $request->rollNo;

			$student = DB::table('students')->where('id', $studentId)->first();

			// Update DOB
			DB::table('students')
				->where('id', $studentId)
				->update([
					'rollno' => $rollNo,
					'updated_at' => now(),
					]);

			return response()->json([
				'status' => 'success',
				'message' => 'Roll number updated successfully.'
			]);

		} catch (\Exception $e) {

			return response()->json([
				'status' => 'fail',
				'message' => 'Something went wrong. Please try again.'
			]);
		}
	}

   	/**
   	 * Update Class Section
   	 * */
   	public function updateSection(Request $request){

		try {

			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');

			$studentId = $request->post('student_id');
			$classId = $request->post('classId');
			$section = $request->post('section');

			$customClassData = DB::table('custom_classes')->where('school_id', $schoolId)->where('class_id', $classId)->where('section' , $section)->get();
			if($customClassData->isNotEmpty()){

				foreach($customClassData as $data){
					Sstudent::where('id', $studentId)->update([
			           'class_id' => $data->class_id,
			           'custom_class_id' => $data->id,
			           'section_id' => $section,
					   'updated_at' => now(),
			        ]);
				}
	        	echo 'Section updated sucessfully';

			}else{
				echo "Selected Section for this class not available";
			}

	    } catch (\Exception $e) {
		    return $e->getMessage();
		}

   	}

	/**
   	 * Update Email id
   	 * */
	public function updateEmailId(Request $request){

		try {
			$request->validate([
				'email' => 'required|email'
			]);

			$studentId = $request->student_id;
			$email = $request->email;
			$student = DB::table('students')->where('id', $studentId)->first();
			// Update Email
			DB::table('students')
				->where('id', $studentId)
				->update([
					'email_id' => $email,
					'email_flag' => '1',
					'updated_at' => now(),
				]);

			return response()->json([
				'status' => 'success',
				'message' => 'Email Id updated successfully.'
			]);

		} catch (\Exception $e) {

			return response()->json([
				'status' => 'fail',
				'message' => 'Something went wrong. Please try again.'
			]);
		}
	}

   	/**
   	 * Add or Register New Student Student.
   	 * */
   	public function addStudent(Request $request){

   		$rules = [
		    'student_name'  => 'required|string|max:100|regex:/^[a-zA-Z][a-zA-Z\s.]*$/u',		    				
		    'studentuid'    => 'required|min:8',
		    'email'         => 'required|email:rfc,dns|max:255',
		    'gender'        => 'required|in:Male,Female',
		    'dob'           => 'required|date',
		    'section'       => 'required|not_in:0',
		    'class'         => 'required|not_in:0',
		    'rollno'        => ['required', 'numeric', 'min:1', 'max:999999',
		    function ($attribute, $value, $fail) use ($request) {
		        $exists = Sstudent::where('class_id', $request->input('class'))->where('school_id', $request->input('schools_id'))->where('academic_year', '2026-2027')->where('section_id', $request->input('section'))->where('rollno', $value)->value('student_name');

		            if ($exists) {
		                $fail('The roll number is already assigned to : ' .$exists );
		            }
		        }
		    ],

		    // 'student_uid'   => ['required', 'digits_between:1,12',
		     'student_uid'   => ['required',
		        function ($attribute, $value, $fail) use ($request) {
		            $existingStudent = Sstudent::where('student_uid', $value) ->where('school_id', $request->input('schools_id'))->where('academic_year', '2026-2027')->whereNotIn('status',['deactivated','inactive'])->first();
		            if ($existingStudent) {
		                $fail('The registration number is already assigned to : ' . ucfirst($existingStudent->student_name));
		            }
		        }
			],
			'is_pwd_add' => 'required|in:0,1',
			'pwdTypesForAdd' => [
				function ($attribute, $value, $fail) use ($request) {
					if ($request->is_pwd_add == 1) {
						if (empty($value) || $value === 'null') {
							$fail('Please select at least one disability type.');
						}
					}
				}
			],
		];

		// Custom validation messages
		$customMessages = [
		    'student_name.required' => 'Please enter student name',
		    'student_name.regex'    => 'Name should contain only letters, spaces, and dots.',
		    'student_name.max'      => 'Maximum character length is 100',

		    'student_uid.required'   => 'Please enter student registration number',
		    'student_uid.numeric'    => 'Registration number should only contain numeric values',
		    // 'student_uid.digits_between' => 'Registration number should be between 1 and 7 digits',

		    'email.required'        => 'Email address is required!',
		    'email.email'           => 'Please provide a valid email address',

		    'gender.required'       => 'Please select gender',
		    'dob.required'          => 'Please select student\'s birth date',
		    'class.required'        => 'Please select class',
		    'section.required'      => 'Please assign class section',

		    'rollno.required'       => 'Please assign roll number to the student',
		    'rollno.numeric'      	=> 'Roll number must be a number.',
		    'rollno.max' 			=> 'Roll number cannot exceed 999999.',
		    'studentuid.required'   => 'Please provide student user id',
		    'studentuid.min'        => 'Student user id must be at least 8 characters',
		];


	    $validator = Validator::make($request->all(), $rules, $customMessages);

	    if ($validator->fails()) {
	        return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
	    }

	    try {

	    	$custom_class_id = DB::table('custom_classes') ->where('class_id', $request->post('class'))
	            ->where('section', $request->post('section'))->where('school_id', $request->post('schools_id'))
	            ->value('id');

	        $words = explode(" ", $request->post('student_name'));
	        $namefirstletter = $words[0];
	        $password = strtolower($namefirstletter) . '@' . $request->post('student_uid');
			$year = date('Y');
            $month = date('m');

            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

			[$disabilities, $pwd_type_id] = array_pad(explode('|', $request->pwdTypesForAdd ?? ''), 2, null);

			if (empty($disabilities) || $disabilities === 'null') {
				$disabilities = null;
			} elseif (is_string($disabilities)) {
				$disabilities = array_filter(array_map('trim', explode(',', $disabilities)));
			}

	        if (!empty($custom_class_id)) {
	            $data = Sstudent::create([
	                'school_id'     => $request->post('schools_id'),
	                'school_code'   => $request->post('school_code'),
	                'student_uid'   => $request->post('student_uid'),
	                'student_name'  => $request->post('student_name'),
	                'gender'        => $request->post('gender'),
	                'class_id'      => $request->post('class'),
	                'custom_class_id'=> $custom_class_id,
	                'section_id'    => $request->post('section'),
	                'dob'           => $request->post('dob'),
	                'user_id'       => $request->post('studentuid'),
	                'password'      => Hash::make($password),
					'password_generated' => 1,
	                'email_id'      => $request->post('email'),
	                'rollno'        => $request->post('rollno'),
	                'status'        => $request->post('status'),
					'is_pwd' => 	$request->is_pwd_add,
					'academic_year' => $academicYear,
	            ]);

				if($request->is_pwd_add == 1){

					DB::table('student_rpwd_mapping')->insert([
						'student_id' => $data->id,
						'pwd_type_id' => $pwd_type_id,
						'anthropo_ht_id' => $request->height_measurement_add ?? null,
						'anthropo_wt_id' => $request->weight_measurement_add ?? null,				
					]);
				}

	            $className = Sclass::find($data->class_id)->value('name');
	            return response()->json([
	                'className' => $className,
	                'data' => $data,
	                'student_name' => $request->post('student_name'),
	                'StudentUserId' => $request->post('studentuid'),
	                'Password' => $password,

	                'status' => 'success',
	                'message' => 'Student added successfully'
	            ]);
	        } else {
	            return response()->json([
	                'status' => 'fail',
	                'message' => 'Selected section for this class is not available'
	            ]);
	        }
	    } catch (\Exception $e) {
	        return response()->json([
	            'status' => 'fail',
	            'message' => 'Failed to add student: ' . $e->getMessage()
	        ]);
	    }
   	}

   	/**
   	 * Get Section based on selected class.
   	 * */
   	public function getClassSection(Request $request){

   		$userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');

   		if($request->ajax()){
   			$classAndSection = ScustomClass::where('school_id',$schoolId)->where('class_id',$request->post('classId'))->select('section')->groupBy('section')->get();
			
			$suggestion = "";
			if($request->input('classId')){
				$suggestion = $this->ageSuggestion($request->post('classId'));
			}

			return response()->json([
				'status' => 'success',
				'data' => $classAndSection,
				'suggestion' => $suggestion
			]);
   			echo $html;
   		}
   	}

   	public function changegender(Request $request){

   		try {
			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');

			if($request->ajax()){
				Sstudent::find($request->post('studentId'))->update([
					'gender' => $request->post('gender'),
					'updated_at' => now(),
				]);

				echo 'Gender updated sucessfully';
			}

		} catch (\Exception $e) {
			$errorMessage = 'Update failed';
	        $httpCode = 500;
	        
	        // Detect database lock errors
	        if (str_contains($e->getMessage(), 'Lock wait timeout') || str_contains($e->getMessage(), 'deadlock') || str_contains($e->getMessage(), 'lock conflict')) {	            
	            $errorMessage = 'The system is currently busy processing other requests. Please try again in a few moments.';
	            $httpCode = 423; 
	        }
	        
	        
	        return response()->json(['error' => $errorMessage], $httpCode);
		}
   	}

   	public function changeStatus(Request $request)
	{

   		try {
			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');

			if($request->ajax()){


				if($request->input('status') && $request->input('status') === 'failed'){				

					$student = Sstudent::find($request->post('studentId'));

					Sstudent::create([
                        'school_id'         => $student->school_id,
                        'school_code'       => $student->school_code,
                        'student_uid'       => $student->student_uid,
                        'student_name'      => $student->student_name,
                        'gender'            => $student->gender,
                        'class_id'          => $student->class_id,
                        'custom_class_id'   => $student->custom_class_id,
                        'section_id'        => $student->section_id,
                        'dob'               => $student->dob,
                        'user_id'           => $student->user_id,
                        'password'          => $student->password,
                        'email_id'          => $student->email_id,
                        'rollno'            => $student->rollno,
                        'domicile'          => $student->domicile,
                        'fav_sport'         => $student->fav_sport,
                        'hobbies'           => $student->hobbies,
                        'apaarId'           => $student->apaarId,
                        'aadhaarId'         => $student->aadhaarId,
                        'passport'          => $student->passport,
                        'status'            => 'active',
                        'academic_year'     => '2026-2027',
                    ]);

                    Sstudent::find($request->post('studentId'))->update([
						'status' => $request->post('status'),
						'updated_at' => now(),
					]);

				}else if($request->input('status') && $request->input('status') === 'transfer'){

					Sstudent::find($request->post('studentId'))->update([
						'status' => $request->post('status'),
						'paymentStatus' => 'unpaid',
						'transfer_date' => now(),
						'updated_at' => now(),
					]);
				}else{

					Sstudent::find($request->post('studentId'))->update([
						'status' => $request->post('status'),
						'revert_date' => now(),
						'updated_at' => now(),
					]);
				}

				echo 'Status updated sucessfully';
			}

		} catch (\Exception $e) {
	        $errorMessage = 'Update failed';
	        $httpCode = 500;
	        
	        // Detect database lock errors
	        if (str_contains($e->getMessage(), 'Lock wait timeout') || str_contains($e->getMessage(), 'deadlock') || str_contains($e->getMessage(), 'lock conflict')) {	            
	            $errorMessage = 'The system is currently busy processing other requests. Please try again in a few moments.';
	            $httpCode = 423; 
	        }
	        
	        // Log::error('Status update failed: '.$e->getMessage());
	        Log::error('Status update failed');
	        return response()->json(['error' => $errorMessage], $httpCode);
	    }
   	}
	


	/**
   	 * 28-05-2025
   	 * Download the template for uploading the student profile data.
   	 * */
   	public function downloadTemplate() {
      	
        $templatePath = public_path('downloads/StudentDataUploadFormat.xlsx');

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($templatePath, 'StudentDataUploadFormat.xlsx', $headers);
    }

    /**
   	 * Download student profile's Sample Data.
   	 * */
    public function sampleData() {
      	

      	// echo "string"; exit();

        $templatePath = public_path('downloads/SampleData.xlsx');

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($templatePath, 'SampleData.xlsx', $headers);
    }


    public function downloadDuplicates(){

    	$filename = Session::get('duplicate_records_file');
    	if (!$filename || !Storage::disk('local')->exists($filename)) {
	        abort(404, 'No failed import records found.');
	    }

	    $duplicateRecords = json_decode(Storage::disk('local')->get($filename), true);
        return Excel::download(new ExportImproperData($duplicateRecords, 'duplicate'), 'DuplicateRecords_'.date("d-m-Y H:i:s").'.xlsx');
    }

    public function ExistingStudents(){

    	$filename = Session::get('existing_records_file');
    	if (!$filename || !Storage::disk('local')->exists($filename)) {
	        abort(404, 'No failed import records found.');
	    }

	    $duplicateRecords = json_decode(Storage::disk('local')->get($filename), true);
        return Excel::download(new ExportImproperData($duplicateRecords, 'duplicate'), 'ExistingRecords_'.date("d-m-Y H:i:s").'.xlsx');
    }

    public function convertRomanToNumeric($roman) {

        $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = 0;
        $length = strlen($roman);

        if (!preg_match('/^[IVX]+$/', $roman)) {            
            if($roman == 'Pre-Nur'){
                $roman = 'Pre Nursery';
            }
            return $roman;
        }

        for($i = 0; $i < $length; $i++) {
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


    public function downloadErrorList(){

    	$filename = Session::get('failed_imports_file');
        if (!$filename || !Storage::disk('local')->exists($filename)) {
	        abort(404, 'No failed import records found.');
	    }

        $failedRecords = json_decode(Storage::disk('local')->get($filename), true);
        return Excel::download(new ExportImproperData($failedRecords,'error_list'), 'ErrorList_'.date("d-m-Y H:i:s").'.xlsx')->deleteFileAfterSend(false);
    }


    public function deleteSelectedClass(Request $request) {

    	$classId = $request->input('class_id');
    	$userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
	    $deleted = \DB::table('custom_classes')->where('class_id', $classId)->where('school_id', $schoolId ?? null)->delete();

	    if ($deleted) {
	        return response()->json(['success' => true]);
	    } else {
	        return response()->json(['success' => false, 'message' => 'Class not found or already deleted.']);
	    }
    }

    public function resetSelectedClass(Request $request) {

	    $userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
	    DB::table('custom_classes')->where('school_id', $schoolId)->delete();
	    return response()->json(['success' => true]);
	}

    /**
     * This method is used to import the data using queue.
     * */
    public function importStudentData(Request $request) {    
        $userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');

		// Step 1: Locking check
	    DB::table('student_import_status')->updateOrInsert(
	        ['school_id' => $schoolId],
	        ['status' => DB::raw('status')]
	    );

	    $statusRow = DB::table('student_import_status')->where('school_id', $schoolId)->first();

	    if ($statusRow->status === 'processing') {
	        $lastUpdated = Carbon::parse($statusRow->updated_at);
	        if ($lastUpdated->diffInMinutes(now()) <= 30) {
	        	return response()->json([
			    	'error' => 'error',
			        'icon' => 'info',
			        'title' => 'Import in Progress',
			        'summary' => 'An import is already in progress. Please wait before uploading again.'
			    ]);
	        }

	        DB::table('student_import_status')->where('school_id', $schoolId)->update(['status' => 'idle']);
	    }

	    /*DB::table('student_import_status')->where('school_id', $schoolId)->update([
	        'status' => 'processing',
	        'updated_at' => now()
	    ]);*/


	    try{


			$validator = Validator::make($request->all(), [
		    	// 'classnomenclature' => 'required',
		        'upload_student_profile' => ['required','file','mimes:xls,xlsx','max:3000',new ExcelHeaderValidation,

		        	function ($attribute, $value, $fail) {
		                if ($value && $value->isValid()) {
		                    try {
		                        $realPath = $value->getRealPath();
		                        $excelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($realPath);
		              
		                        if (!$excelReader->canRead($realPath)) {
		                            $fail('The template format is invalid or corrupted. Please open the file in Excel, use "Save As", select "Excel Workbook (.xlsx)" and re-upload.');
		                            return;
		                        }

		                        $excelReader->listWorksheetInfo($realPath);

		                    } catch (\Throwable $ex) {

		                       $fail('<strong>File layout configuration error detected.</strong><br><br>' .
				                 '<strong>How to correct your file:</strong><br>' .
				                 '<ol style="margin-top: 5px; padding-left: 20px;">' .
				                 '  <li>Open your current file on your computer using <strong>Microsoft Excel</strong> or <strong>Google Sheets</strong>.</li>' .
				                 '  <li>Navigate to the top menu, click on <strong>File</strong>, and select <strong>Save As</strong> (or <em>Download > Microsoft Excel (.xlsx)</em> if using Google Sheets).</li>' .
				                 '  <li>In the file type dropdown menu, explicitly choose <strong>Excel Workbook (*.xlsx)</strong>.</li>' .
				                 '  <li>Provide a new name for the file, click <strong>Save</strong>, and upload this new version instead.</li>' .
				                 '</ol>');
		                    }
		                }
		            }
		           
		        ],
		    ], 
		    [
		        'upload_student_profile.required'=> 'Please select a document file.',
		        'upload_student_profile.file'	 => 'No file has been selected. Please choose a file.',
		        'upload_student_profile.mimes' 	 => 'The selected file must be in either the xls or xlsx format.',
		        'upload_student_profile.max' 	 => 'File size should not be more than 3MB.',			
		    ]);


			if ($validator->fails()){
	        	
	            $errorContent = '<ul style="list-style-type: none;">';
	            $dynamicTitle = 'Validation Error'; 


	            foreach ($validator->errors()->getMessages() as $field => $validationErrors) {
			        foreach ((array)$validationErrors as $validationError) {
			            $errorContent .= '<li>'.$validationError.'</li>';		            
			            // Set dynamic title based on error type
			            // if (str_contains($validationError, 'file name')) {
			            //     $dynamicTitle = 'Filename Mismatch';
			            // } else

			            if (str_contains($validationError, 'headers')) {
			                $dynamicTitle = 'Template Format Error';
			            } elseif (str_contains($validationError, 'size')) {
			                $dynamicTitle = 'File Too Large';
			            }elseif (str_contains($validationError, 'corrupted') || str_contains($validationError, 'layout')) {
		                    $dynamicTitle = 'Corrupted Template File';
		                }
			        }
			    }

	            $errorContent .= '</ul>';

	            return response()->json([
			    	'error' => 'error',
			        'icon' => 'error',
			        'title' => $dynamicTitle,
			        'summary' => $errorContent
			    ]);
	        }


	        $file = $request->file('upload_student_profile');
			$action = $request->post('event');        
			$loggedInSchoolCode = \App\Models\School::find($schoolId)?->school_code;


	        $importData = new ImportStudentProfile($schoolId, $action, $userId,$logId ='');
			$dataArray = Excel::toArray($importData, $file);
			$totalRecords = count($dataArray[0]);
			$sheetData = $dataArray[0];


			$seenCombos = [];
			$excelDuplicates = 0;
			$uniqueInExcel = [];
			$excelDuplicatesData = [];
			$invalidRows = [];		    
			$validRows = [];

			foreach ($sheetData as $index => $row) {
		    	$errors = [];

		    	/* Check for empty rows */
		    	$isEmptyRow = true;
				foreach ($row as $cellValue) {
					if (trim((string)$cellValue) !== '') {
						$isEmptyRow = false;
						break;
					}
				}

				if ($isEmptyRow) {
					$invalidRows[] = array_merge($row, [
						'Row' => $index + 2,
						'Error' => 'Blank row found at row '. $index + 2 .' . Please Delete empty rows and try again.'
					]);
					continue;
				}

				/* Check Missing School Code and Admission number */
		    	$schoolCode = strtoupper(trim($row['school_code'] ?? ''));
				$admissionNo = strtoupper(trim($row['admissionnumber'] ?? ''));	

				if (empty($admissionNo)) {
			        $invalidRows[] = array_merge($row, [
			            'Row'   => $index + 2,
			            'Error' => 'Admission number is missing.',
			        ]);
			        continue;
			    }

			    $combo = $schoolCode . '|' . $admissionNo;
		        if (isset($seenCombos[$combo])) {
		            $excelDuplicates++;
		            $excelDuplicatesData[] = $row;
		            $invalidRows[] = array_merge($row, [
			            'Row' => $index + 2,
			            'Error' => 'Duplicate admission number with same school code found in Excel.',
			        ]);
			        continue;
		        } else {
		            $seenCombos[$combo] = true;
		            $uniqueInExcel[] = $row;
		        }

		        /* Validate School Code */
		        $school_code = trim($row['school_code'] ?? null);
		        if (empty($school_code)) {
				    $errors[] = "School code number is missing.";
				} else if ($school_code !== $loggedInSchoolCode) {
				    $errors[] = "School code does not match the logged-in school code";
				} else if (!preg_match('/^\S+$/', $school_code)) {
				    $errors[] = "School code should not contain whitespace";
				}

		        /* Validate Admission Number */
			    $admissionnumber = trim($row['admissionnumber'] ?? null);
			    if (empty($admissionnumber)) {
				    $errors[] = "Student Admission Number number is missing.";
				}else if (!preg_match('/^[a-zA-Z0-9\/\\\\-]+$/', $admissionnumber)) {
			        $errors[] = "Admission number can only contain letters, numbers, /, -, or \\ characters.";
			    }
		       
			    /* Validate Name */
			    $name = trim($row['name'] ?? null);			   
			    if (empty($name)) {
				    $errors[] = "Student Name is missing.";
				} else if (!preg_match('/^[a-zA-Z][a-zA-Z\s.\']*$/u', $name)) {
				    $errors[] = "Name should contain only letters, spaces, dots and apostrophes, and should not start with whitespace.";
				} else if (mb_strlen($row['name']) > 100) {
				    $errors[] = "Name should not exceed 100 characters and should not contain leading or trailing white spaces.";
				}

			    /* Validate Class */
			    $validRomanClasses = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
			    $class = trim($row['class'] ?? null);
			    if (empty($class)) {
				    $errors[] = "Class is missing.";
				}else if (!in_array($class, $validRomanClasses)) {
			        $errors[] = "Class should must be a capital Roman numeral between I and XII only. Found: '{$class}'.";
			    }

			    /* Validate Gender */
			    $validGender = ['M', 'F','m', 'f', 'Male', 'Female', 'male','female','MALE','FEMALE'];
			    $gender = strtoupper(trim($row['gender'] ?? null));
			    if (empty($gender)) {
				    $errors[] = "Gender is missing.";
				}else if (!in_array($gender, $validGender)) {
			        $errors[] = "Gender should only be Male, Female, M, or F.";
			    }

			    /* Validate Section */
			    $section = trim($row['section'] ?? null);
			    if (empty($section)) {
				    $errors[] = "Section is missing.";
				}else if (!preg_match('/^[a-zA-Z0-9]+([ -][a-zA-Z0-9]+)*$/', $section)) {
					$errors[] = "Section can only contain letters, numbers, single spaces, and single hyphens.";
				}

			    /* Validate Roll Number */
			    $rollNo = trim($row['roll_no'] ?? null);
			    if (empty($rollNo)) {
				    $errors[] = "Roll Number is missing.";
				}else if (!preg_match('/^[0-9]+$/', $rollNo)) {
			        $errors[] = "Roll no. should be numeric value only";
			    }

			    /* Validate Date of Birth */		
			    $dateValue = trim($row['dob_ddmmyyyy'] ?? null);
				$validDate = null;

				if (empty($dateValue)) {
			    	$errors[] = "Date of Birth is missing.";
				}else if (is_numeric($dateValue)) {
			        $carbonDate = Carbon::instance(Date::excelToDateTimeObject($dateValue));
			        $validDate = $carbonDate->format('d/m/Y');
			    } elseif (preg_match('/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/', $dateValue)) {				        
			        $dateValue = str_replace('-', '/', $dateValue);
			        $carbonDate = Carbon::createFromFormat('d/m/Y', $dateValue);
			        if ($carbonDate && $carbonDate->format('d/m/Y') === $dateValue) {
			            $validDate = $dateValue;
			        }
			    }

			    if (!$validDate) {
			        $errors[] = "Invalid date format: $dateValue";
			    }


				$email = trim($row['email'] ?? null);
				if (empty($email)) {
					$errors[] = "Email is missing.";
				} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$errors[] = "Invalid Email format '{$email}'. Please enter a valid email like example@domain.com.";
				} else {
					// Extract domain
					$domain = strtolower(substr(strrchr($email, '@'), 1));
					// Check MX record
					if (!checkdnsrr($domain, 'MX')) {
						$errors[] = "Invalid email domain '{$domain}'. The domain does not have a valid mail server.";
					}
				}

			    /* Validate PWD */
			    $is_pwd = trim($row['cwsn'] ?? null);
			    if (!in_array($is_pwd, ['YES', 'NO'], true)) {
					$errors[] = "Invalid 'cwsn' value '{$is_pwd}'. Allowed values: NO (Normal), YES (Person with disabilities).";
				}

			    if (!empty($errors)) {
			        $invalidRows[] = array_merge($row, [
			            'Row' => $index + 2,
			            'Error' => implode(' | ', $errors),
			        ]);
			    } else {
			        $validRows[] = $row;
			    }
		    }

		    // 2. Check which unique records exist in database
		    $dbDuplicates = [];
		    $paidDuplicates = [];
		    $chunkSize = 1000;

		    foreach(array_chunk($uniqueInExcel, $chunkSize) as $chunk){

		        $conditions = array_map(function($row) {
				    return [
				        'student_uid' => strtoupper(trim($row['admissionnumber'])),
				        'school_code' => strtoupper(trim($row['school_code'])),
				    ];
				}, $chunk);

		        $alreadyPaid= DB::table('students')
		        ->where('school_id', (string) $schoolId)
		        ->where('academic_year', '2026-2027')
				->where('paymentStatus', 'paid')
		        ->where(function ($query) use ($conditions) {
				    foreach ($conditions as $condition) {
				        $query->orWhere(function ($subQuery) use ($condition) {
				            $subQuery->where('school_code', $condition['school_code'])
				                     ->where('student_uid', $condition['student_uid']);
				        });
				    }
				})
	            ->get(['school_code', 'student_uid as admissionnumber','student_name as name', 'gender','class_id as class', 'section_id as section','email_id as email','dob as dob_ddmmyyyy','rollno as roll_no']);

				$existing = DB::table('students')
		        ->where('school_id', (string) $schoolId)
		        ->where('academic_year', '2026-2027')
		        ->where(function ($query) use ($conditions) {
				    foreach ($conditions as $condition) {
				        $query->orWhere(function ($subQuery) use ($condition) {
				            $subQuery->where('school_code', $condition['school_code'])
				                     ->where('student_uid', $condition['student_uid']);
				        });
				    }
				})
	            ->get(['school_code', 'student_uid as admissionnumber','student_name as name', 'gender','class_id as class', 'section_id as section','email_id as email','dob as dob_ddmmyyyy','rollno as roll_no']);

	            $dbDuplicates = array_merge($dbDuplicates, $existing->toArray());
	            $paidDuplicates = array_merge($paidDuplicates, $alreadyPaid->toArray());
		    }

		    $newRecordsCount = count($uniqueInExcel) - count($dbDuplicates);

			if(count($paidDuplicates) > 0){
				
				$timestamp = now()->format('YmdHis');
				$duplicateFilename = "duplicate_records/{$loggedInSchoolCode}_{$timestamp}.json";
				Storage::disk('local')->put($duplicateFilename, json_encode($paidDuplicates));
				Session::put('duplicate_records_file', $duplicateFilename);

				$confirmButtonText = "OK";
				$buttonClass = 'btn-cancel';
				$summary = '<p>The Excel file contains details of '.$totalRecords.' students. Payment for '.count($paidDuplicates).' of these students has already been made through the Career Portal for your school.
				<br><strong>Therefore, overwriting the data for these students will not be possible. </strong> <br><a href="'.route('downloadDuplicates').'"> click to view</a></p>';
				return response()->json([
					'error' => 'error',
					'icon' => 'info',
					'title' => 'Invalid Data Found',
					'summary' => $summary
				]);
			}

		    /* Check for the same class */
		    $sameClassStudents = [];

		    $studentUids = collect($validRows)->pluck('admissionnumber')->map(fn($v) => strtoupper(trim($v)))
		    ->unique()->values()->toArray();

		    $previousStudents = DB::table('students')
		    ->where('school_id', (string)$schoolId)
		    ->where('academic_year', '2025-2026')
		    ->where('status', 'active')
		    ->whereIn('student_uid', $studentUids)
		    ->get([
		        'student_uid',
		        'student_name',
		        'class_id',
		        'section_id'
		    ])->keyBy('student_uid');


		    foreach ($validRows as $row) {

			    $admissionNo = strtoupper(trim($row['admissionnumber']));
			    $section = strtoupper(trim($row['section'] ?? ''));
			    $uploadedClass = trim($row['class']);
			    $newClassId = $this->romanToNumber($uploadedClass);

			    $oldStudent = $previousStudents[$admissionNo] ?? null;

			    if (!$oldStudent || !$newClassId) {
			        continue;
			    }

			    if ((int)$newClassId === (int)$oldStudent->class_id) {

			        $sameClassStudents[] = [
					    'school_code'         => $row['school_code'] ?? '',
					    'admissionnumber'     => $admissionNo,
					    'name'                => $row['name'] ?? '',
					    'gender'              => $row['gender'] ?? '',
					    'class'               => $uploadedClass,
					    'section'             => $section,
					    'roll_no'             => $row['roll_no'] ?? '',
					    'dob_ddmmyyyy'    	  => $row['dob_ddmmyyyy'] ?? '',
					    'email'               => $row['email'] ?? '',
					    'rpwd'                => $row['rpwd'] ?? '',
					    'apaarId'             => $row['apaarId'] ?? '',
					    'Error'              => 'Student will continue in same class and will be marked as failed in Academic Year 2025-2026.'
					];
			    }
			}


	        if($action == 'preview'){

	        	$summary = '';
	        	$sameClassSummary = '';

	    		$summary = 'The Excel file contains '.$totalRecords . ' records. Please review and confirm to proceed with the import.';
		        $confirmButtonText = "Yes, Import it!";
		        $buttonClass = 'btn-import';

				/* Validate Rows  */
		        if (!empty($invalidRows)) {

		        	$timestamp = now()->format('Ymd');
					$errorFilename = "invalid_excel/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($errorFilename, json_encode($invalidRows));
					Session::put('invalid_rows_file', $errorFilename);

					$summary = "<p>The Excel file contains ".$totalRecords." records, out of which " . count($invalidRows) . " rows have errors.<a href='".route('downloadInvalidData')."'> Click to view the errors</a>. Please correct them and upload the file again.</p>" ;

		           	return response()->json([
				    	'error' => 'error',
				        'icon' => 'info',
				        'title' => 'Invalid Data Found',
				        'summary' => $summary
				    ]);
				}


		        if ($excelDuplicates > 0) {
		        	$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "duplicate_records/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($duplicateFilename, json_encode($excelDuplicatesData));
					Session::put('duplicate_records_file', $duplicateFilename);

					$summary = '<p>The Excel file contains '.$totalRecords.' records with '.$excelDuplicates.' duplicate entries within the file	<a href="'.route('downloadDuplicates').'"> click to view</a></p>';
		        }

				

		        if(count($dbDuplicates) > 0){
		        	$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "duplicate_records/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($duplicateFilename, json_encode($dbDuplicates));
					Session::put('duplicate_records_file', $duplicateFilename);

		        	//Storage::disk('local')->put('duplicate_records.json', json_encode($duplicates));
		        	$confirmButtonText = "Overwrite it!";
		        	$buttonClass = 'btn-overwrite';
		        	$summary = '<p>The Excel file contains '.$totalRecords.' records including '.count($dbDuplicates).' entries already exist in database. <a href="'.route('downloadDuplicates').'"> click to view</a></p>';
		        }


		        if($excelDuplicates > 0 && count($dbDuplicates) > 0){

		        	$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "duplicate_records/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($duplicateFilename, json_encode($excelDuplicatesData));
					Session::put('duplicate_records_file', $duplicateFilename);

		        	$confirmButtonText = "Yes, Overwrite it!";
		        	$buttonClass = 'btn-overwrite';
		        	$summary = '<p>The Excel file contains '.$totalRecords.' records including '.count($dbDuplicates).'duplicate entries already exist in database and '.$excelDuplicates.' duplicate entries within the file	<a href="'.route('downloadDuplicates').'"> click to view</a></p>

	    			<div class="form-group mt-2">
	                    <label>Do you want to overwrite existing records?</label>
	                    <input type="radio" id="overwrite" name="importOption" value="override" data-id="Yes, Overwrite it!" checked>
	                    <label for="overwrite">Yes, Overwrite it</label><br>
	                    <input type="radio" id="skip" name="importOption" value="skipandimport" data-id="Skip & Import">
	                    <label for="skip">Skip Overwrite & Import New Records Only</label>
	                </div>';
		        }


		       	if(!empty($dbDuplicates) && $newRecordsCount > 0){

		       		$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "duplicate_records/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($duplicateFilename, json_encode($dbDuplicates));
					Session::put('duplicate_records_file', $duplicateFilename);

		        	$confirmButtonText = "Yes, Overwrite it!";
		        	$buttonClass = 'btn-overwrite';
		        	$summary = '<p>The Excel file contains '.$totalRecords.' records including '.count($dbDuplicates).' duplicate entries
	        			<a href="'.route('downloadDuplicates').'"> click to view</a></p>

	        			<div class="form-group mt-2">
	                        <label>Do you want to overwrite existing records?</label>
	                        <input type="radio" id="overwrite" name="importOption" value="override" data-id="Yes, Overwrite it!" checked>
	                        <label for="overwrite">Yes, Overwrite it</label><br>
	                        <input type="radio" id="skip" name="importOption" value="skipandimport" data-id="Skip & Import">
	                        <label for="skip">Skip Overwrite & Import New Records Only</label>
	                    </div>';

		        }


		        /* check duplicacy in db and new reocrds in excel */
		        if (!empty($dbDuplicates) && $newRecordsCount > 0 && !empty($sameClassStudents) ) { 
		        	$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "existing_records/{$loggedInSchoolCode}_{$timestamp}.json";
					
					Storage::disk('local')->put($duplicateFilename, json_encode($sameClassStudents));
					Session::put('existing_records_file', $duplicateFilename);
		        	
		        	$confirmButtonText = "Yes, Overwrite it!";
		        	$buttonClass = 'btn-overwrite';

		        	$summary = '
		        	<p>The Excel file contains <strong>'.$totalRecords.' records </strong> including '.count($dbDuplicates).' entries already present for 2026-2027 <a href="'.route('downloadDuplicates').'"> click to view</a></p>
		        	<p>and <strong>'.count($sameClassStudents).' students</strong>
				            were found continuing in the same class for the new academic session.
				            These students will be imported successfully but they will get marked as Failed. Please verify before proceeding.
				            <a href="'.route('existingStudents').'"> click to view</a> 
				        </p>

				        <div class="form-group mt-2">
	                        <label>Do you want to continue with this records?</label>
	                        <input type="radio" id="import" name="importOption" value="override" data-id="Yes, Overwrite it!" required>
	                    </div>';
				}

				/* Check same class students in 2026-2027 */
				if (!empty($sameClassStudents)) { 

		        	$timestamp = now()->format('YmdHis');
		        	$duplicateFilename = "existing_records/{$loggedInSchoolCode}_{$timestamp}.json";
					Storage::disk('local')->put($duplicateFilename, json_encode($sameClassStudents));
					Session::put('existing_records_file', $duplicateFilename);
		        	$confirmButtonText = "Yes, Import it!";
		        	$buttonClass = 'btn-overwrite';

	        		$summary = '
						<p>
							The uploaded Excel file contains <strong>'.$totalRecords.' records</strong>. 
							Among them, <strong>'.count($sameClassStudents).' students</strong> were identified as continuing in the same class for the new academic session.
						</p>

						<p>
							These students will be imported successfully; however, they will automatically be marked as <strong>Failed</strong>. 
							Please review and verify the details before proceeding with the import.
						</p>

						<p>
							<a href="'.route('existingStudents').'">
								Click to view
							</a>
						</p>
					';
				}



		       	return response()->json(['summary' => $summary,'icon' => 'info','cnfmText' => $confirmButtonText,'btnclass'=>$buttonClass]);



	        } else if($action == 'import' || $action == 'skipandimport'){

				$file = $request->file('upload_student_profile');
				$timestamp = now()->format('YmdHis');
				$filename = $loggedInSchoolCode . '_' . $timestamp . '.' . $file->getClientOriginalExtension();
				$storedPath = $file->storeAs('import_students', $filename);

				$uploadLog = \App\Models\StudentImportLog::create([
				    'school_id'  => $schoolId,
				    'user_id'    => $userId,
				    'file_path'  => $storedPath,
				    'filename'   => $filename,
				    'status'     => 'queued',
				    'action'     => $action,
				    'is_active'  => 'active',
				    'started_at' => now(),
				]);

				AuditHelper::log('upload_activity', $action);

				ProcessStudentImport::dispatch($schoolId, $action, $userId, $storedPath, $uploadLog->id)->onQueue('upload_test');

				DB::table('school_password_jobs')->updateOrInsert(
					['school_id' => $schoolId],
					[
						'status' => 'pending',
						'updated_at' => now(),
						'created_at' => now(),
					]
				);
	        	$summary = 'The import process has been initiated and queued. Kindly check after some time for the updated results.';
			    return response()->json(['summary' => $summary, 'icon' => 'success']);

	        } else if($action == 'override'){

				$file = $request->file('upload_student_profile');
				$timestamp = now()->format('YmdHis');
				$filename = $loggedInSchoolCode . '_' .$timestamp. '.' . $file->getClientOriginalExtension();
				$storedPath = $file->storeAs('import_students', $filename);

				$uploadLog = \App\Models\StudentImportLog::create([
				    'school_id'  => $schoolId,
				    'user_id'    => $userId,
				    'file_path'  => $storedPath,
				    'filename'   => $filename,
				    'status'     => 'queued',
				    'action'     => $action,
				    'is_active'  => 'active',
				    'started_at' => now(),
				]);

				AuditHelper::log('upload_activity', $action);

				ProcessStudentImport::dispatch($schoolId, $action, $userId, $storedPath, $uploadLog->id)->onQueue('upload_test');
				DB::table('school_password_jobs')->updateOrInsert(
					['school_id' => $schoolId],
					[
						'status' => 'pending',
						'updated_at' => now(),
						'created_at' => now(),
					]
				);
	        	$summary = 'The import process has been initiated and queued. Kindly check after some time for the updated results.';
			    return response()->json(['summary' => $summary, 'icon' => 'success']);	

	        }
	        
	    } catch (\Throwable $e) {
	   		
        	$errorMessage = 'Import failed';
	        $httpCode = 500;
	        
	        // Detect database lock errors
	        if (str_contains($e->getMessage(), 'Lock wait timeout') || str_contains($e->getMessage(), 'deadlock') || str_contains($e->getMessage(), 'lock conflict')) {	            
	            $errorMessage = 'The system is currently busy processing other requests. Please try again in a few moments.';
	            $httpCode = 423; 
	        }

	        DB::table('student_import_status')->where('school_id', $schoolId)->update(['status' => 'idle','updated_at' => now()]);
	        
	        \Log::error("Import Error for School_id ".$schoolId  .": " . $e->getMessage());

	        return response()->json([
		    	'error' => 'error',
		        'icon' => 'info',
		        'title' => $httpCode,
		        'summary' => $errorMessage
		    ]);

	    } 
	    /*finally {
	        DB::table('student_import_status')->where('school_id', $schoolId)->update(['status' => 'idle', 'updated_at' => now()]);
	    }*/
    }


    protected function romanToNumber($roman) {

	    $map = [
	        'I'   => 1,
	        'II'  => 2,
	        'III' => 3,
	        'IV'  => 4,
	        'V'   => 5,
	        'VI'  => 6,
	        'VII' => 7,
	        'VIII'=> 8,
	        'IX'  => 9,
	        'X'   => 10,
	        'XI'  => 11,
	        'XII' => 12,
	    ];

	    return $map[strtoupper(trim($roman))] ?? null;
	}
	
	public function downloadUploadedFile($logId) {

        $log = StudentImportLog::findOrFail($logId);
	    $filePath = $log->file_path; 

	    if (!$filePath || !Storage::disk('local')->exists($filePath)) {
	        abort(404, 'File not found.');
	    }

	    return Storage::disk('local')->download( $filePath, basename($filePath),
	        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
	    );
    }



	public function downloadErrorFile($logId) {

	    $log = StudentImportLog::findOrFail($logId);
	    $filePath = $log->error_file; 
	    $fileName = pathinfo($filePath, PATHINFO_FILENAME);

	    if (!$filePath || !Storage::disk('local')->exists($filePath)) {
	        abort(404, 'File not found.');
	    }

	    // Read JSON file and convert to array
	    $jsonData = json_decode(Storage::disk('local')->get($filePath), true);

	    if (!is_array($jsonData)) {
	        abort(500, 'Invalid JSON data.');
	    }

	    // Download as Excel file
	    return Excel::download(new ExportImproperData($jsonData, 'error_list'), $fileName . '.xlsx');
	}

	/**
	 * 19-11-2025
	 * Generate identity Card. 
	 * */

	public function getClassSectionSummary(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id'
        ]);

        $studentIds = $request->input('student_ids');

        $classSummary = DB::table('students')
            ->leftJoin('class', 'students.class_id', '=', 'class.id')
            ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
            ->whereIn('students.id', $studentIds)
            ->where('students.status', 'active')
            ->select(
                DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                END AS class_name"),
                'custom_classes.section',
                DB::raw('COUNT(students.id) as student_count')
            )
            ->groupBy('class_name', 'custom_classes.section')
            ->orderBy('class_name')
            ->orderBy('custom_classes.section')
            ->get();

        // Group by class for summary
        $groupedSummary = [];
        foreach ($classSummary as $item) {
            $className = $item->class_name;
            if (!isset($groupedSummary[$className])) {
                $groupedSummary[$className] = [
                    'class_name' => $className,
                    'sections' => [],
                    'total_students' => 0
                ];
            }
            $groupedSummary[$className]['sections'][] = [
                'section' => $item->section,
                'student_count' => $item->student_count
            ];
            $groupedSummary[$className]['total_students'] += $item->student_count;
        }

        return response()->json([
            'class_summary' => array_values($groupedSummary),
            'total_selected' => count($studentIds)
        ]);
    }

	public function generateIdCard(Request $request){
		
		set_time_limit(0);
		ini_set('memory_limit', '-1');

		try {
			$request->validate([
				'student_ids'   => 'required|array',
				'student_ids.*' => 'integer|exists:students,id',
			]);

			$studentIds = $request->student_ids;

			$students = DB::table('schools')
				->join('students', 'students.school_id', 'schools.id')
				->join('class', 'class.id', 'students.class_id')
				->select(
					'schools.school_name',
					'schools.logo',
					'schools.school_code',
					'schools.address',
					'students.student_name',
					'students.gender',
					'students.student_uid',
					'class.name as class_name',
					'students.section_id'
				)
				->where('students.status', 'active')
				->whereIn('students.id', $studentIds)
				->get();

			if ($students->isEmpty()) {
				return response()->json([
					'error' => true,
					'message' => 'No active students found'
				], 404);
			}

			// 🔹 Attach QR code once
			foreach ($students as $student) {
				$romanClass = \App\Helpers\Helper::changeToRoman($student->class_name);

				$qrText =
					$student->student_name . "\n" .
					$romanClass . '-' . $student->section_id . "\n" .
					'Goforfit Id: ' .
					$student->school_code . $student->student_uid;

				$qr = QrCode::format('png')
					->size(70)
					->encoding('UTF-8')
					->errorCorrection('M')
					->generate($qrText);

				$student->qr_base64 = 'data:image/png;base64,' . base64_encode($qr);
			}

			$signature = DB::table('usermetas')
				->where('user_id', auth()->id())
				->value('signature');

			$grouped = $students->groupBy(fn ($s) =>
				$s->class_name . '_' . $s->section_id
			);

			$zipPath = tempnam(sys_get_temp_dir(), 'zip');
			$zip = new ZipArchive;

			if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
				throw new \Exception('Unable to create ZIP file');
			}

			foreach ($grouped as $group) {
				$first = $group->first();

				$className = preg_replace('/[^A-Za-z0-9\-]/', '-', $first->class_name);
				$sectionName = 'Section-' . $first->section_id;

				$pdf = Pdf::loadView('school.generate.studentIcard', [
					'students'  => $group,
					'signature' => $signature
				])->setPaper('A4');

				$zip->addFromString(
					"{$className}/{$sectionName}_student_icards.pdf",
					$pdf->output()
				);
			}

			$zip->close();

			return response()
				->download($zipPath, 'student_I-Cards.zip')
				->deleteFileAfterSend(true);

		} catch (\Throwable $e) {
			\Log::error('ID Card Generation Failed', [
				'error' => $e->getMessage()
			]);

			return response()->json([
				'error' => true,
				'message' => 'Something went wrong while generating ID cards'
			], 500);
		}
	}

	// generate students login credentials 

	public function generateClassSectionCredentials(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'export_type'   => 'required|in:single,separate',
        ]);

        $studentIds = $request->input('student_ids', []);
        $exportType = $request->input('export_type', 'single');
		
		// echo "<pre>"; print_r($studentIds);exit();

        $activeCount = DB::table('students')
            ->whereIn('id', $studentIds)
            ->where('status', 'active')
            ->count();

        if ($activeCount === 0) {
            return response()->json(['error' => true, 'message' => 'No active students found']);
        }

        if ($exportType === 'single') {
            $export = new StudentsCredentialsExport($studentIds);
            $fileName = 'students-credentials-all-classes.xlsx';
            return Excel::download($export, $fileName);
        } else {
            return $this->generateClassFolderStructure($studentIds);
        }
    }


    public function downloadStudentProfile(Request $request) {
	    try {

	        $userId = Auth::id();

	        $schoolId = DB::table('school_reference')
	            ->where('school_user_id', $userId)
	            ->where('status', 1)
	            ->value('school_id');

	        $studentIds = [];
	        $selectAll = true;

	       
	      	$selectedYear = $request->input('selectedYear');
	        if(empty($selectedYear)){
	        	$selectedYear = '2025-2026';
	        }

	        if ($request->filled('student_ids')) {
	            $studentIds = array_map('intval', (array) $request->student_ids);
	            if (empty($studentIds)) {
	                return response()->json([
	                    'status' => false,
	                    'message' => 'No students selected.'
	                ], 400);
	            }
	            $selectAll = false;
	        }
	        $fileName = 'StudentDataUploadFormat' . now()->format('Ymd_His') . '.xlsx';

	        return Excel::download( new ExportStudentProfile(  $schoolId, $studentIds, $selectAll, $selectedYear), $fileName );

	    } catch (\Throwable $e) {

	        \Log::error('Student export failed: ' . $e->getMessage());

	        return response()->json([
	            'status' => false,
	            'message' => $e->getMessage(),
	            'line' => $e->getLine(),
	            'file' => $e->getFile(),
	        ], 500);
	    }
	}

    private function generateClassFolderStructure($studentIds){
		
        $classesWithSections = DB::table('students')
            ->leftJoin('class', 'students.class_id', '=', 'class.id')
            ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
            ->whereIn('students.id', $studentIds)
            ->where('students.status', 'active')
            ->select(
                DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                END AS class_name"),
                'custom_classes.section'
            )
            ->distinct()
            ->orderBy('class_name')
            ->orderBy('custom_classes.section')
            ->get();

        if ($classesWithSections->isEmpty()) {
            return response()->json(['error' => true, 'message' => 'No class-section data found']);
        }

        $classGroups = [];
        foreach ($classesWithSections as $item) {
            $className = $item->class_name;
            if (!isset($classGroups[$className])) {
                $classGroups[$className] = [];
            }
            $classGroups[$className][] = $item->section;
        }

        $zipFileName = 'students-credentials-class-wise.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($classGroups as $className => $sections) {
                $cleanClassName = $this->cleanFileName($className);
                
                foreach ($sections as $section) {
                    // Create export for each class-section
                    $export = new StudentsCredentialsExport(
                        $studentIds, 
                        $className, 
                        $section
                    );
                    
                    $cleanSection = $this->cleanFileName($section);
                    $fileName = "Section {$cleanSection}.xlsx";
                    $folderPath = "{$cleanClassName}/";
                    
                    // Store Excel file temporarily
                    $tempFileName = "temp_{$cleanClassName}_{$cleanSection}.xlsx";
                    Excel::store($export, 'temp/' . $tempFileName);
                    $tempExcelPath = storage_path('app/temp/' . $tempFileName);
                    
                    // Add to zip with folder structure
                    if (file_exists($tempExcelPath)) {
                        $zip->addFile($tempExcelPath, $folderPath . $fileName);
                    }
                }
            }
            
            $zip->close();
            
            // Clean up temporary Excel files
            foreach ($classGroups as $className => $sections) {
                $cleanClassName = $this->cleanFileName($className);
                foreach ($sections as $section) {
                    $cleanSection = $this->cleanFileName($section);
                    $tempFileName = "temp_{$cleanClassName}_{$cleanSection}.xlsx";
                    $tempExcelPath = storage_path('app/temp/' . $tempFileName);
                    
                    if (file_exists($tempExcelPath)) {
                        unlink($tempExcelPath);
                    }
                }
            }
            
            // Return zip file as download
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => true, 'message' => 'Failed to create zip file'], 500);
    }

    private function cleanFileName($name)
    {
        // Clean filename - remove special characters and replace with underscores
        return preg_replace('/[^a-zA-Z0-9\-]/', '-', $name);
    }

	
	/**
	 * Convert the mulitple file into Zip.
	 * */
	private function createZipAndDownload($pdfFiles, $ClassSection)
	{
		$zipFileName = $ClassSection.'.'.'zip';
		$zip = new ZipArchive();

		//Path where the zip file will be temporarily stored
		$zipFilePath = storage_path("app/temp/{$zipFileName}");

		if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) 
		{
			//Add each PDF file to the ZIP archive
			foreach ($pdfFiles as $pdfFile) 
			{
				$fileName = basename($pdfFile); // Extract the file name
				$zip->addFile($pdfFile, $fileName);
			}

			// Close the ZIP archive
			$zip->close();
		}

		// Download the ZIP file
		return response()->download($zipFilePath)->deleteFileAfterSend(true);
	}
   

	public function addClassNomenclature(Request $request){
		
		$classes = $request->input('classes', []);
		$created = [];

		$userId = Auth::id();
		$schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');

		// Loop over submitted classes
		foreach ($classes as $class) {
			if (!isset($class['id']) || !isset($class['nomenclature'])) {
				continue; // skip incomplete entries
			}

			// Check if the class already exists for this school
			$existing = ScustomClass::where('school_id', $schoolId)->where('class_id', $class['id'])->first();

			if (!$existing) {
	
				$maxValue = ScustomClass::max('orders') ?? 0;
				$newClass = new ScustomClass();
				$newClass->school_id = $schoolId;
				$newClass->class_id = $class['id'];
				$newClass->section = 'A';     //default section A
				$newClass->nomenclature = $class['nomenclature'];
				$newClass->orders = $maxValue;
				$newClass->status = 1;
				$newClass->save();

				$created[] = [
					'id' => $newClass->class_id,
					'nomenclature' => $newClass->nomenclature
				];
			}
		}

		return response()->json([
			'status' => 'success',
			'message' => count($created) . ' class(es) created.',
			'createdClasses' => $created
		]);
	}

	// get districts
	public function getDistrictList(Request $request) {

        $stateId = $request->post('stateId');
        $cities = District::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json($cities);
    }

	// get zones 
	public function getZones(Request $request) {

        $regionId = $request->post('regionId');
        $zones = DB::table('zones')->where('region_id', $regionId)->get(['id', 'name']);
        return response()->json($zones);
    }


    /**
     * Manage Trainers.
     * */
	public function RegisterTrainer(Request $request){

        $state = State::where('id', $request->state)->first();          
        $district = District::where('id', $request->district)->first();


        $validator = Validator::make($request->all(), [
            'trainerName' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Transgender',
            'qualification' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|unique:users,phone|digits:10',
            'state' => 'required|integer|exists:states,id',
            'district' => 'required|integer|exists:districts,id',
            'city' => 'required|string|max:255',
            'block' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'marketing_consent' => 'accepted',
            'privacy_policy' => 'accepted',
            'term_condition' => 'accepted',
            'dob' => ['required', 'date', 'after_or_equal:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $dob = \Carbon\Carbon::parse($value);  $age = $dob->age;

                    if ($request->has('term_condition') && $age < 18) {
                        $fail('You must be at least 18 years old to accept the Terms and Conditions.');
                    }
                }],
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $dob = $request->post('dob');
        $password = str_replace('-', '', $dob);


        
        $teacher = Teacher::create([
            'name' => $request->trainerName,
            'email' => $request->email,
            'phone' => $request->mobile,
            'position' => 'Trainer',
            'role_id' => 3,
            'status'  => 1, //$LoginStatus,
            'password' => Hash::make($password),
        ]);

        $school_id = DB::table('school_reference')->where('school_user_id', auth()->id())->value('school_id');
        
        if($teacher){ 
            $usermeta = new Usermeta();
            $usermeta->user_id = $teacher->id;
            $usermeta->state = $state->name;
            $usermeta->state_id = $state->id;
            $usermeta->district = $district->name;
            $usermeta->district_id = $district->id;
            $usermeta->city = $request->city;
            $usermeta->school_name = null;
            $usermeta->school_id = $school_id;
            $usermeta->created_by = Auth::user()->id ?? null;
            $usermeta->class = null;
            $usermeta->subject = null;
            $usermeta->qualification = $request->qualification;
            $usermeta->dob = $request->dob;
            $usermeta->achievement = $request->achievement ?? null;
            $usermeta->gender = $request->gender;    
            $usermeta->save();
        }



        
	    $exists = DB::table('school_trainers')->where('trainer_id', $request->trainer_id)->where('school_id', $school_id)->exists();
	    if ($exists) {
	        return response()->json(['message' => 'Trainer is already mapped to this school.'], 200);
	    }

	    // add trainer with the school and keep it unmap as default mapping
	    DB::table('school_trainers')->insert([
	        'trainer_id' => $teacher->id,
	        'school_id' => $school_id,
	        'status' => 0,
	        'created_at' => now(),
	        'updated_at' => now(),
	        'mapped_on'  => now(),
	    ]);

        $teacher->save();
        $serial = str_pad($teacher->id, 7, '0', STR_PAD_LEFT);
        $self_registrationId = 'GFFT' . $serial;
        $teacher->update(['self_registrationId' => $self_registrationId]);

        /*
        $recipient = [
            'email' => $teacher->email,
            'name' => $teacher->name,
        ];
        $subject = 'Welcome to GoForFit, '. $teacher->name .' – Your Registration Is Successful!';
        $htmlContent = $view->make('emails.self_registration_mail', [
            'user' => $teacher,
            'password' => $password,
            'registrationId' => $self_registrationId,
        ])->render();

        $emailService->send($recipient, $subject, $htmlContent);
        */

        return back()->with('success', 'Trainer saved successfully!');
    }


    public function MapTrainer(Request $request) {

		$title = 'Manage Trainer';	
		$SchoolUserId  =  \Auth::id();
		$school_id = DB::table('school_reference')->where('school_user_id',$SchoolUserId)->where('status', 1)->value('school_id');
		
		$mappedTrainers = DB::table('school_trainers as map')
	    ->join('users as u', 'u.id', '=', 'map.trainer_id')
	    ->where('map.school_id', $school_id)
	    ->select('u.id','u.self_registrationId as self_registrationId','u.name', 'u.email','u.phone', 
	        DB::raw("CASE WHEN map.status = 1 THEN 'Mapped' ELSE 'Unmapped' END as status")
	    )->get();


        // echo "<pre>"; print_r($mappedTrainers);exit();

		if($request->ajax()){

			$request->validate([
		        'trainer_id' => 'required|exists:users,id'
		    ]);

		    $school_id = DB::table('school_reference')->where('school_user_id', auth()->id())->value('school_id');
		    $exists = DB::table('school_trainers')
			    ->where('trainer_id', $request->trainer_id)
			    ->where('school_id', $school_id)
			    ->where('status', 0)
			    ->exists();

		    /* This part of the code is used for temprory purpose need to be clear later once Reset Password functionality is activated */
		    
		    $users = DB::table('usermetas as um')
			    ->join('users as u', 'u.id', '=', 'um.user_id')
			    ->select( 'um.user_id', 'um.dob',
			        DB::raw("
			            CASE 
			                WHEN um.dob REGEXP '^[0-9]+$' 
			                THEN DATE_ADD('1899-12-30', INTERVAL um.dob DAY)
			                ELSE um.dob
			            END AS converted_dob
			        "),

			        DB::raw("
			            DATE_FORMAT(
			                CASE 
			                    WHEN um.dob REGEXP '^[0-9]+$' 
			                    THEN DATE_ADD('1899-12-30', INTERVAL um.dob DAY)
			                    ELSE um.dob
			                END,
			                '%Y%m%d'
			            ) AS new_password
			        ")
			    )
			    ->where('u.id', $request->trainer_id)
			    ->first();

			if(!empty($users)){
				DB::table('users')->where('id', $users->user_id)->update([
	                'password' => Hash::make($users->new_password)
	            ]);
			}


		    if ($exists) {
		    	DB::table('school_trainers')->where('trainer_id', $request->trainer_id)->where('school_id', $school_id)
	                ->update([
	                    'status' => 1,
	                    'updated_at' => now(),
	                    'mapped_on' => now(),
	                ]);
	            return response()->json(['message' => 'Trainer mapped successfully!'], 200);
		    }

		   
		    DB::table('school_trainers')->insert([
		        'trainer_id' => $request->trainer_id,
		        'school_id' => $school_id,
		        'status' => 1,
		        'created_at' => now(),
		        'updated_at' => now(),
		        'mapped_on'  => now(),
		    ]);
			

			return response()->json(['message' => 'Trainer mapped successfully!'], 200);
		}

        $state_list  = State::orderBy('name', 'asc')->get();   
        $cities = District::orderBy('name', 'asc')->get(['id', 'name']);
        return view('school.map-trainer', compact('title','mappedTrainers','state_list','cities'));
	}



	public function UnMapTrainer(Request $request) {

	
	    $title = 'DeActivate Trainer';
	    $schoolUserId = auth()->id();
	    $school_id = DB::table('school_reference')->where('school_user_id', $schoolUserId)->where('status', 1)->value('school_id');

	    if ($request->ajax()) {

	        $request->validate([
	            'trainer_id' => 'required|exists:users,id',
	        ]);

	        $trainerId = $request->trainer_id;
	        $exists = DB::table('school_trainers')->where('trainer_id', $trainerId)->where('school_id', $school_id)->where('status', 1)->exists();

	        if ($exists) {
	          
	            DB::table('school_trainers')->where('trainer_id', $trainerId)->where('school_id', $school_id)
	                ->update([
	                    'status' => 0,
	                    'updated_at' => now(),
	                    'unmapped_on' => now(),
	                ]);

	            return response()->json(['message' => 'Trainer unmapped successfully!'], 200);
	        } else {
	            return response()->json(['message' => 'Trainer mapping not found.'], 404);
	        }
	    }
	}

	public function RemoveTrainer(Request $request) {

		if($request->ajax()){

			$trainer_id = $request->trainer_id;
			$school_id = DB::table('school_reference')->where('school_user_id', auth()->id())->value('school_id');
			
			DB::table('school_trainers')->where('school_id', $school_id)->where('trainer_id', $trainer_id)->delete();

		    return response()->json(['message' => 'Trainer Removed successfully!'], 200);
		}

	}


	// uodated on 17Sep auto complete dropdown 

	public function autocomplete(Request $request) {
	    $query = $request->get('term');
	    $result = [];

		$schoolUserId = auth()->id();
	    $school_id = DB::table('school_reference')->where('school_user_id', $schoolUserId)->where('status', 1)->value('school_id');		
	    $dbQuery = DB::table('users')->where('self_registrationId','<>', null );


	    if (strpos($query, '@') !== false) {
	       
	        $trainers = $dbQuery->where('email', 'like', "%{$query}%")->limit(10)->get();
	        foreach ($trainers as $trainer) {
				$trainer_id = $trainer->id;
				$activated = DB::table('school_trainers')->where('trainer_id',$trainer_id)->where('school_id',$school_id)->where('status',1)->exists();
				$maskedMobile = str_repeat("X", strlen($trainer->phone) - 4) . substr($trainer->phone, -4);
				$result[] = [
	                'label' => $trainer->name, 
	                'value' => $trainer->name,
	                'userid' => $trainer->id,
	                'id'    => $trainer->self_registrationId,
	                'name'  => $trainer->name,
	                'email' => $this->maskEmail($trainer->email),
	                'phone' => $maskedMobile,
					'activated'=>$activated,
	            ];
	        }

	    } elseif (preg_match('/^GFFT[0-9\-]*$/i', $query)) {
	        // $trainers = DB::table('users')->where('self_registrationId', 'like', "%{$query}%")->limit(10)->get();

	        $trainers = $dbQuery->where('self_registrationId', 'like', "%{$query}%")->limit(10)->get();

	        foreach ($trainers as $trainer) {
				$trainer_id = $trainer->id;
				$activated = DB::table('school_trainers')->where('trainer_id',$trainer_id)->where('school_id',$school_id)->where('status',1)->exists();
				$maskedMobile = str_repeat("X", strlen($trainer->phone) - 4) . substr($trainer->phone, -4);
				$result[] = [
	                'label' => $trainer->name, 
	                'value' => $trainer->name,
	                'userid' => $trainer->id,
	                'id'    => $trainer->self_registrationId,
	                'name'  => $trainer->name,
	                'email' => $this->maskEmail($trainer->email),
	                'phone' => $maskedMobile,
					'activated'=>$activated,
	            ];
	        }

	    } else {
 
	        // $trainers = DB::table('users')->where('name', 'like', "%{$query}%")->limit(10)->get();
	        $trainers = $dbQuery->where('name', 'like', "%{$query}%")->limit(14)->get();
			
	        foreach ($trainers as $trainer) {
				$trainer_id = $trainer->id;
				$activated = DB::table('school_trainers')->where('trainer_id',$trainer_id)->where('school_id',$school_id)->where('status',1)->exists();
				$maskedMobile = str_repeat("X", strlen($trainer->phone) - 4) . substr($trainer->phone, -4);
				$result[] = [
	                'label' => $trainer->name, 
	                'value' => $trainer->name,
	                'userid' => $trainer->id,
	                'id'    => $trainer->self_registrationId,
	                'name'  => $trainer->name,
	                'email' => $this->maskEmail($trainer->email),
	                'phone' => $maskedMobile,
					'activated'=>$activated,
	            ];
	        }
	    }

	    return response()->json($result);
	}

	public function maskEmail($email) {
		$parts = explode("@", $email);
		$name = $parts[0];
		$domain = $parts[1];

		$visible = substr($name, 0, 4);
		$masked = str_repeat("*", max(0, strlen($name) - 4));

		return $visible . $masked . '@' . $domain;
	}

	public function viewProfile($id = null){

        $title = 'School Profile';  
        $board_list  = Board::orderBy('boardname', 'asc')->get();

		$regions = Region::all();
		$zones = DB::table('zones')->get();
		$districts = DB::table('districts')->get();
		$states = DB::table('states')->get();
		
    	$userId = '';
		if($id){
			$userId = DB::table('school_reference')->where('school_id',$id)->value('school_user_id');
		}else{
			$userId  = Auth::user()->id;
		}
    	$schoolData = DB::table('school_reference')
    		->select(
    			'users.id as user_id',
    			'schools.id as id',
    			'schools.board',
    			'schools.school_code',
    			'schools.region',
    			'schools.zonename',
    			'schools.state',
    			'schools.district',
    			'schools.city',
    			'schools.school_name',
    			'schools.shift',

    			'schools.school_email as s_email',
    			'schools.logo',
    			'schools.address',
    			'schools.pincode',

    			'schools.school_phone as s_contact',
    			'schools.school_url',
    			// 'schools.description',

    			'schools.school_principal as p_name',
    			'users.userid as p_email',
    			'users.email as head_email',
    			'users.phone as p_contact',
    			'users.profile_picture',
    			'users.position as p_designation',
				'usermetas.state_id',
    			'usermetas.gender as p_gender',
    			'usermetas.signature',
    			'usermetas.hod_sign',
    			'usermetas.shortinfo'
    		)

    		->join('users','users.id','=', 'school_reference.school_user_id')
    		->join('schools','schools.id','=', 'school_reference.school_id')
    		->join('usermetas','usermetas.user_id', '=', 'users.id')
    		->where('users.status', 1)
    		->where('users.id', $userId)
    		->first();

			$hod = json_decode($schoolData->shortinfo, true);

        return view('school.profile.index', compact('title','board_list','regions','zones', 'states', 'districts', 'schoolData', 'hod'));
    }

    /* updated profile picture 14_apr */

    public function UpdateProfile(Request $request, $id) {
		
		$request->validate([
			'city' => 'nullable|string',

			'school_code' => 'required|string|max:100',
			'school_name' => 'required|string|max:255',
			'school_shift' => 'nullable',
			'school_email' => 'nullable|email',
			'head_email' => 'nullable|email',
			'school_contact' => 'nullable|digits:10',
			'school_url' => 'nullable|string',
			'school_address' => 'required|string',
			
			'principalName' => 'required|string',
			'principalEmail' => 'nullable|email',
			'schoolAdminDesignation' => 'required',
			'gender' => 'required',
			'principal_contact' => 'nullable|string',

			'hodName' => 'required|string',
			'hodEmail' => 'nullable|email',
			'hodContact' => 'nullable|digits:10',

			'school_logo' => 'nullable|image|mimes:jpg,jpeg,png|no_jfif|max:1024',
			'principal_signature' => 'nullable|file|image|mimes:jpg,jpeg,png|no_jfif|max:300',
			'hod_signature' => 'nullable|file|image|mimes:jpg,jpeg,png|no_jfif|max:300',
			'profilePicture' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
		],[
			'principal_signature.max' => 'The principal\'s signature must not exceed 300KB.',
			'hod_signature.max' => 'The HOD\'s signature must not exceed 300KB.',
			'school_logo.max' => 'School logo size must be less than 1MB',
			'profilePicture.max' => 'Profile image must be smaller than 2MB',
			'*.no_jfif' => 'JFIF images are not allowed.',
		]);
		
		$userId = '';
		if(Auth::user()->role_id==9){
			$userId = DB::table('school_reference')->where('school_id', $id)->value('school_user_id');
		}else{
			$userId = Auth::user()->id;
		}

		// echo "<pre>"; print_r($request->all());exit();

		if (isset($request->state) && !empty($request->state)) {
			list($stateId, $stateName) = explode('|', $request->state);
		} else {
			$stateId = $stateName = null;
		}

		if (isset($request->region) && !empty($request->region)) {
			list($regionName, $regionId) = explode('|', $request->region);
		} else {
			$regionId = $regionName = null;
		}

		$districts = DB::table('districts')->where('id', $request->district)->value('name');

		$hodDetails = json_encode([
			'name'    => $request->hodName,
			'email'   => $request->hodEmail,
			'contact' => $request->hodContact,
		]);

        $data = [			
        	'school_principal'	=> $request->principalName,
            'school_email'    => $request->school_email,
            'school_phone'    => $request->school_phone,
            'school_url'      => $request->school_url,
            'address'         => $request->school_address,
            'school_principal'=> $request->principalName,
            'shift'         => $request->school_shift,
		];

		if(Auth::user()->role_id == 9){
			$data['school_name']  = $request->school_name;
			$data['school_code']  = $request->school_code;			
            $data['region']       = $regionName;
            $data['zonename']     = $request->zonename;
            $data['state']        = $stateName;
            $data['district']     = $districts;
            $data['city']         = $request->city;

		}


        DB::table('schools')->where('id', $id)->update($data);


        DB::table('users')->where('id', $userId)->update([
        	'name'     => $request->principalName,
        	'email'     => $request->head_email,
            'userid'    => $request->principalEmail,
            'phone'    => $request->principal_contact,
            'position' => $request->schoolAdminDesignation,
        ]);
		// echo "<pre>"; print_r("user update");exit();
		
   		DB::table('usermetas')
        ->where('user_id', $userId)->update([
            'school_id' 	=> $id,
            'school_name' 	=> $request->school_name,
            'gender'    	=> $request->gender,
            'dob'           => $request->dob ?? '',
			'state_id' 		=> $stateId,			
			'shortinfo'		=> $hodDetails
        ]);

        if ($request->hasFile('school_logo')) {
			$logo_path = $this->resizeAndSaveImage($request->file('school_logo'), 100, 'logos' , $id);
            DB::table('schools')->where('id', $id)->update([
				'logo' => $logo_path,
	        ]);
        }
		
		if ($request->hasFile('profilePicture')) {
            $imagePath = Helper::resizeAndSaveImage($request->file('profilePicture'), 100, 'profilePictures/users' , $userId);
			DB::table('users')->where('id', $userId)->update([
	            'profile_picture' => $imagePath,
	        ]);
        }

        if ($request->hasFile('principal_signature')) {
			$signaturePath = $this->resizeAndSaveImage($request->file('principal_signature'), 100, 'signatures' , $id);
			
            DB::table('usermetas')->where('user_id', $userId)->update([
	            'signature' => $signaturePath,
	        ]);
        }
		if ($request->hasFile('hod_signature')) {
			$signaturePath = $this->resizeAndSaveImage($request->file('hod_signature'), 100, 'hod-signatures' , $id);
			
            DB::table('usermetas')->where('user_id', $userId)->update([
	            'hod_sign' => $signaturePath,
	        ]);
        }
		
		
		if (Auth::user()->role_id == 9) {
			AuditHelper::log('School profile', 'School id: ' . $id . '. Profile updated by CISCE');
			return redirect()->route('cisce.update.profile', ['id' => $id])->with([
				'success' => 'School Records updated successfully.',
				'clearForm' => true
			]);
		}else{		
			AuditHelper::log('School profile', 'School profile updaed by School');	
			return redirect()->route('school.profile')->with([
				'success' => 'School Records updated successfully.',
				'clearForm' => true
			]);
		}
    }


	private function resizeAndSaveImage($file, $height, $folder, $id){
	
		$path = public_path("assets/uploads/{$folder}/");

		
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		$extension = strtolower($file->getClientOriginalExtension());
		$filename = $id . '_' . $folder . '.' . $extension;
		$destination = $path . $filename;

		// echo "<pre>"; print_r($destination);exit();

		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				$src = imagecreatefromjpeg($file->getRealPath());
				break;
			case 'png':
				$src = imagecreatefrompng($file->getRealPath());
				break;
			case 'webp':
				$src = imagecreatefromwebp($file->getRealPath());
				break;
			default:
				throw new \Exception("Unsupported file type: $extension");
		}


		$origWidth = imagesx($src);
		$origHeight = imagesy($src);

		$width = intval(($height / $origHeight) * $origWidth);

		$dst = imagecreatetruecolor($width, $height);

		if (in_array($extension, ['png', 'webp'])) {
			imagealphablending($dst, false);
			imagesavealpha($dst, true);
			$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
			imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);
		}

		imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($dst, $destination, 80);
				break;
			case 'png':
				imagepng($dst, $destination, 6);
				break;
			case 'webp':
				imagewebp($dst, $destination, 80);
				break;
		}

		imagedestroy($src);
		imagedestroy($dst);

		return $filename;
	}


	
	
	public function ViewFitnessReport($id) {

	    $studentId = Crypt::decryptString($id);
	    $studentsData = $this->getStudentData($studentId);
	    $TermMasterId = $this->getTermId($studentsData->schools_id);

	    $dob          = Carbon::parse($studentsData->dob);
	    $studentAge   = $dob->age;
	    $studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
	    $ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));

	    // Fetch report + benchmarks
	    $reportData    = $this->getReportData($studentId);
	    $mappedReport  = $this->mapReportData($reportData, $studentAge, $studentGender, $ageGender);

	    $groupedReport = $mappedReport->groupBy('Category');


	    // echo "<pre>"; print_r($studentsData); exit();
		$getBmiBenchmark = $getBmiBenchmark =  $this->getBmiBenchmark($ageGender);

	    $higherClasses = [4,5,6,7,8,9,10,11,12];
	    if (in_array($studentsData->class_id, $higherClasses)) {

			[$orderedReportData, $getFitnessBenchmark] = $this->getSeniorReportData($studentId, $studentAge, $studentGender, $groupedReport);
	        return view('assessor.reports.senior-report', compact('studentsData','orderedReportData','getFitnessBenchmark','getBmiBenchmark'
	        ));

	    } else {

            [$orderedReportData, $FmsReportData, $getFitnessBenchmark] =
            $this->getJuniorReportData($studentId, $studentAge, $studentGender, $groupedReport, $TermMasterId);
            // echo "<pre>"; print_r($getFitnessBenchmark);exit();

	        return view('assessor.reports.junior-report', compact('studentsData','orderedReportData','FmsReportData','getFitnessBenchmark','getBmiBenchmark'));
	    }
	}
	

	// trainers credentials 19-11-2025

	public function exportSchoolUsers(Request $request) {

	    $schoolUserIds = $request->input('school_user_ids', []);
	    if(empty($schoolUserIds)) {

	        return response()->json(['message' => 'No users selected'], 400);
	    }

	    $timestamp = Carbon::now()->format('Ymd_His');
		if($request->role=='trainer'){
			$fileName = "trainers-credential_{$timestamp}.xlsx";
			return Excel::download(new TrainerCredentials($schoolUserIds), $fileName);
		}
		
	}

	// for age validation
    private function isValidAge($class, $age){

		$classDetails = DB::table('class')->where('id', $class)->first();

		if (!$classDetails) {
			return false; 
		}

		$minAge = $classDetails->min_age;
		$maxAge = $classDetails->max_age;

		return ($age >= $minAge && $age <= $maxAge);
	}

	private function ageSuggestion($class){
		$classDetails = DB::table('class')->where('id', $class)->first();

		$suggestions = [];
 		$suggestions = [$classDetails->min_age, $classDetails->max_age];
		
		return $suggestions;

	}

	// roll number suggestions 
	public function rollNoSuggestion(Request $request){

		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

		$assignedRollNumbers = DB::table('students')
			->where('school_id', (string)$request->schoolId)
			->where('academic_year', $academicYear)
			->where('status', 'active')
			->where('class_id', $request->class_id)
			->where('section_id', $request->section_id)
			->whereNotNull('rollno')
			->pluck('rollno')
			->map(function ($num) {
				return (int) $num;
			})
			->toArray();

		$maxAssigned = !empty($assignedRollNumbers) ? max($assignedRollNumbers) : 0;

		$studentCount = DB::table('students')
			->where('school_id', (string)$request->schoolId)
			->where('academic_year', $academicYear)
			->where('status', 'active')
			->where('class_id', $request->class_id)
			->where('section_id', $request->section_id)
			->count();

		$maxRange = max($maxAssigned, $studentCount);

		$suggestions = [];

		for ($i = 1; $i <= $maxRange; $i++) {
			if (!in_array($i, $assignedRollNumbers)) {
				$suggestions[] = $i;
				if (count($suggestions) >= 5) break;
			}
		}

		$next = $maxRange + 1;
		while (count($suggestions) < 5) {
			if (!in_array($next, $assignedRollNumbers)) {
				$suggestions[] = $next;
			}
			$next++;
		}

		return response()->json([
			'suggested_roll_numbers' => $suggestions
		]);
	}

	// to edit student details 

	public function EditStudentDetails(Request $request){
		try {

			$student = DB::table('students')->where('id', $request->s_id)->first();
			if (!$student) {
				return response()->json(['status' => 'fail', 'message' => 'Student not found.']);
			}
			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;

			$rules = [
				'editStudentName' => 'required|string|max:100|regex:/^[a-zA-Z][a-zA-Z\s.]*$/u',
				'editStudentEmail' => 'required|email:rfc,dns|max:255',
				'editStudentSection' => 'required',
				'editStudentGender' => 'required|in:Male,Female',
				'editStudentStatus' => 'required|in:active,transfer',
				'editStudentRollno' => [
					'required',
					'numeric',
					'min:1',
					'max:999999',
					function ($attribute, $value, $fail) use ($request, $student, $academicYear) {
						$exists = Sstudent::where('class_id', $request->input('editStudentClass'))
							->where('school_id', (string)$student->school_id)
							->where('academic_year', $academicYear)
							->where('status', 'active')
							->where('section_id', $request->editStudentSection)
							->where('rollno', $value)
							->where('id', '!=', $request->s_id)
							->value('student_name');

						if ($exists) {
							$fail('The roll number is already assigned to : ' . $exists);
						}
					}
				],
				'editStudentDOB' => 'required|date',
				'is_pwd' => 'required|in:0,1',
				'pwdTypes' => [
					function ($attribute, $value, $fail) use ($request) {
						if ($request->is_pwd == 1) {
							if (empty($value) || $value === 'null') {
								$fail('Please select at least one disability type.');
							}
						}
					}
				],
			];

			$customMessages = [
				'editStudentName.required' => 'Please enter student name.',
				'editStudentName.regex' => 'Name should contain only letters, spaces, and dots.',
				'editStudentName.max' => 'Maximum character length is 100.',
				'editStudentEmail.required' => 'Email address is required!',
				'editStudentEmail.email' => 'Please provide a valid email address.',
				'editStudentGender.required' => 'Please select gender.',
				'editStudentDOB.required' => 'Please select student\'s birth date.',
				'editStudentSection.required' => 'Please select section.',
				'editStudentRollno.required' => 'Please assign a roll number to the student.',
				'editStudentRollno.numeric' => 'Roll number must be a number.',
				'editStudentRollno.min' => 'Roll number must be at least 1.',
				'editStudentRollno.max' => 'Roll number cannot exceed 999999.',
				'editStudentStatus.in' => 'Student status must be either active or transfer.',
			];

			$validator = Validator::make($request->all(), $rules, $customMessages);

			if ($validator->fails()) {
				return response()->json([
					'status' => 'fail',
					'error' => $validator->errors()
				]);
			}
			$age = \Carbon\Carbon::parse($request->editStudentDOB)->age;
			$isValidAge = $this->isValidAge($request->editStudentClass, $age);

			if (!$isValidAge && !$request->has('force_update')) {

				$classDetails = DB::table('class')
					->where('id', $request->editStudentClass)
					->first();

				return response()->json([
					'status' => 'age_warning',
					'message' => "Age should be between {$classDetails->min_age} to {$classDetails->max_age} years for Class-{$request->editStudentClass}. Do you want to proceed?"
				]);
			}

			$custom_class_id = DB::table('custom_classes')
				->where('school_id', $student->school_id)
				->where('class_id', $request->editStudentClass)
				->where('section', $request->editStudentSection)
				->value('id');

			if (!$custom_class_id) {
				return response()->json([
					'status' => 'fail',
					'message' => 'Custom class not found.'
				]);
			}
			// list($disabilities, $pwd_type_id) = explode('|', $request->pwdTypes);
			[$disabilities, $pwd_type_id] = array_pad(explode('|', $request->pwdTypes ?? ''), 2, null);
			
			
			if (empty($disabilities) || $disabilities === 'null') {
				$disabilities = null;
			} elseif (is_string($disabilities)) {
				$disabilities = array_filter(array_map('trim', explode(',', $disabilities)));
			}

			$words = explode(" ", $request->editStudentName);
			$namefirstletter = $words[0];
			$password = strtolower($namefirstletter) . '@' . $request->editStudentAdmissionNo;

			DB::table('students')->where('id', $request->s_id)->update([
				'student_name' => $request->editStudentName,
				'email_id' => $request->editStudentEmail,
				'gender' => $request->editStudentGender,
				'password' => Hash::make($password),
				'dob' => $request->editStudentDOB,
				'class_id' => $request->editStudentClass,
				'custom_class_id' => $custom_class_id,
				'section_id' => $request->editStudentSection,
				'rollno' => $request->editStudentRollno,
				'status' => $request->editStudentStatus,
				'is_pwd' => $request->is_pwd,
				'updated_at' => now(),
			]);

			if($request->is_pwd == 1){

				DB::table('student_rpwd_mapping')->updateOrInsert([	'student_id' => $request->s_id,],
					[
						'pwd_type_id' => $pwd_type_id,
						'anthropo_ht_id' => $request->height_measurement_edit ?? null,
						'anthropo_wt_id' => $request->weight_measurement_edit ?? null,
					]
				);
			}

			return response()->json([
				'status' => 'success',
				'message' => 'Student details updated successfully.'
			]);

		} catch (\Exception $e) {
			\Log::error('EditStudentDetails failed', [
				'message' => $e->getMessage(),
			]);

			return response()->json([
				'status' => 'fail',
				'message' => 'Something went wrong. Please try again.'
			]);
		}
	}


	public function StudentPromotionHistory(Request $request){
		$userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		$data = ScustomClass::where('school_id', $schoolId)->select('class_id','section')->get()->toArray();
		$customClass1 = array();	

		foreach($data as $value){
			$class = $value['class_id'];
			$section = $value['section'];
			if(!isset($customClass1[$class])){
				$customClass1[$class] = [];
			}
			$customClass1[$class][] = $section;
		}

		$school = School::find($schoolId);
		$classList = $school->getClasses;

		foreach ($classList as $class) {
			$originalClass = Sclass::where('id', $class->class_id)->orderBy('orders')->first();
			$class->name = !empty($class->nomenclature) 
				? $class->nomenclature 
				: ($originalClass ? $originalClass->name : null);
		}
		$classList = $classList->sortBy('orders')->values();
		$classList->prepend((object)[
			'class_id' => '',
			'name' => 'Select Class',
			'section' => ''
		]);

		$sub = DB::table('custom_classes')
			->select(DB::raw('MIN(id) as min_id'))
			->where('school_id', $schoolId)
			->groupBy('class_id');

		$classes = DB::table('custom_classes')
		->join('class', 'class.id', '=', 'custom_classes.class_id')
		->join('schools', 'schools.id', '=', 'custom_classes.school_id')
		->whereIn('custom_classes.id', $sub)
		->select(
			'schools.id as schools_id',
			'custom_classes.id as custom_class_id',
			'custom_classes.class_id as id',
			'custom_classes.section',
			DB::raw("
				CASE 
					WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
					THEN custom_classes.nomenclature 
					ELSE class.name 
				END AS className
			")
		)
		->orderBy('school_id')->orderby('custom_classes.orders')
		->get();

		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

			[$startYear, $endYear] = explode('-', $academicYear);
			$previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

		
		$studentsQuery = DB::table('students')
		->leftJoin('class', 'students.class_id', '=', 'class.id')
		->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->select(
			'students.id as student_id',
			'students.student_uid as admissionnumber',
			'students.rollno',
			'students.student_name as student_name',
			'students.class_id',
			'students.section_id',
			'students.custom_class_id',				
			'students.status',
			DB::raw("CASE 
					WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
					THEN custom_classes.nomenclature 
					ELSE class.name 
				END AS display_classname"),
			'custom_classes.section',        	
		)
		->where('students.school_id', (string) $schoolId)
		->where('students.academic_year', $academicYear)
		->where('students.status', 'active')
		->orderBy('students.class_id')
		->orderBy('students.section_id')
		->orderBy('students.rollno', 'asc');	

		$admissionNumbers = $studentsQuery->pluck('admissionnumber')->toArray();

		$previousYearStudents = DB::table('students')
		->leftJoin('class', 'students.class_id', '=', 'class.id')
		->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->select(
			'students.student_uid',
			'students.rollno',
			'students.section_id',

			DB::raw("CASE 
				WHEN custom_classes.nomenclature IS NOT NULL 
					AND custom_classes.nomenclature <> '' 
				THEN custom_classes.nomenclature 
				ELSE class.name 
			END AS prev_classname")
		)
		->where('students.school_id', (string) $schoolId)
		->where('students.academic_year', $previousAcademicYear)
		->whereIn('students.student_uid', $admissionNumbers)
		->get()
		->keyBy('student_uid');
		
		if ($request->filled('class_id')) {
			$studentsQuery->where('students.class_id', $request->class_id);
		}

		if ($request->filled('section_id')) {
			$studentsQuery->where('students.section_id', $request->section_id);
		}

		if ($request->has('status')) {
			$status = $request->input('status');

			if (!empty($status)) {
				$studentsQuery->where('students.status', $status);
			}
		}

		$studentsDetails = $studentsQuery->get();

		if($request->ajax()){

			return Datatables::of($studentsDetails)
			->addIndexColumn()
			->addColumn('class_id', function($row) {
				return $row->display_classname;
			})

			->addColumn('section_id', function($row){
				return $row->section_id;
			})
			
			->addColumn('rollno', function($row) {
				return $row->rollno;
			})

			->addColumn('prev_class', function ($row) use ($previousYearStudents) {
				$student = $previousYearStudents[$row->admissionnumber] ?? null;
				return $student
					? $student->prev_classname
					: '---';
			})

			->addColumn('prev_section', function ($row) use ($previousYearStudents) {
				$student = $previousYearStudents[$row->admissionnumber] ?? null;
				return $student
					? $student->section_id
					: '---';
			})

			->addColumn('prev_rollno', function ($row) use ($previousYearStudents) {
				$student = $previousYearStudents[$row->admissionnumber] ?? null;
				return $student
					? $student->rollno
					: '---';
			})	

			->addColumn('action', function($row){
				return '<button class="btn btn-sm btn-primary revert-student" 
								data-id="'.$row->student_id.'" >
								Revert
						</button>';
			})

			->rawColumns(['class_id', 'section_id', 'rollno', 'prev_class','prev_section','prev_rollno', 'dob', 'status', 'action'])
			->toJson();
		}

		$title = 'Students Promotion History';

		return view('school.students-promotion-history', compact('title', 'classList', 'studentsDetails', 'classes','data'));
	}

	public function RevertStudentPromotion(Request $request){
		try {
			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')
						->where('school_user_id', $userId)
						->where('status', 1)
						->value('school_id');
			$studentId = $request->student_id;
			$admissionNo = Sstudent::where('id', $studentId)
							->value('student_uid');

			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;
			[$startYear, $endYear] = explode('-', $academicYear);
			$previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

			$studentOldRecord = Sstudent::where('school_id', (string) $schoolId)
								->where('academic_year', $previousAcademicYear)
								->where('student_uid', $admissionNo)
								->where('id', '!=', $studentId)
								->first();

			if (!$studentOldRecord) {
				return response()->json([
					'success' => false,
					'message' => 'Student record not found for Previous academic year.'
				]);
			}

			$deleted = Sstudent::where('id', $studentId)
						->where('academic_year', $academicYear)
						->delete();

			if ($studentOldRecord) {
				$studentOldRecord->status = 'active';
				$studentOldRecord->save();

				return response()->json([
					'success' => true,
					'message' => 'Student promotion reverted successfully.'
				]);
			}

			return response()->json([
				'success' => false,
				'message' => 'Previous academic year record not found.'
			]);

		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'Something went wrong while reverting student promotion.'
			], 500);
		}
	}

	/**
	 * Date : 29-09-26
	 * Function: Create School's Admin.
	 * */
	public function viewSchoolAdmin() {

		$title = 'Delegate Portal Access';

		$user = Auth::user();
		$DashboardModule = DashboardModule::select('role_id','name','slug','route_name','icon','order_no','status')
		->whereIn('role_id', [0, $user->role_id])
		->where('status',1)->orderBy('order_no')->get();

        $school_id = DB::table('school_reference')->where('school_user_id', $user->id)->where('status', 1)->value('school_id');

		$schoolUsers = DB::table('school_reference as sref')
			->join('users as u', 'u.id', '=', 'sref.school_user_id')
			->join('usermetas as um', 'um.user_id', '=', 'u.id')
			->leftJoin('dashboard_modules as m', function ($join) {
				$join->whereRaw("JSON_CONTAINS(um.module_access, JSON_QUOTE(m.route_name))");
			})
			->where('sref.school_id', $school_id)
			->where('u.role_id', 8)
			->select(
				'u.id',
				'u.self_registrationId',
				'u.userid',
				'u.name',
				'u.email',
				'u.phone',
				'u.position',
				'um.gender',
				'um.dob',
				'um.qualification',
				DB::raw("GROUP_CONCAT(DISTINCT m.name ORDER BY m.order_no ASC SEPARATOR ', ') as module_names"),
				DB::raw("CASE WHEN sref.status = 1 THEN 'Active' ELSE 'Inactive' END as status")
			)
			->groupBy(
				'u.id','u.self_registrationId','u.userid','u.name','u.email',
				'u.phone','um.gender','um.dob','um.qualification','u.position','sref.status'
			)
			->get();

	   	// echo "<pre>"; print_r($schoolUsers);exit();

	
		$state_list  = State::orderBy('name', 'asc')->get();   
        $cities = District::orderBy('name', 'asc')->get(['id', 'name']);
		$SchoolDetails = DB::table('schools')->select('school_name','logo','school_code')->where('id', $school_id)->first();


		return view('school.admin.view-school-admin', compact('title','state_list','cities','DashboardModule','schoolUsers','SchoolDetails'));
	}

	private function generateUserId() {
	    $prefix = 'GFFSU';
	    $lastUser = User::orderBy('id', 'desc')->first();

	    if ($lastUser && $lastUser->userid) {
	        preg_match('/\d+$/', $lastUser->userid, $matches);
	        $number = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
	    } else {
	        $number = 1;
	    }
	    $userid = $prefix . str_pad($number, 7, '0', STR_PAD_LEFT);
	    return $userid;
	}

	public function storeSchoolAdmin(Request $request) {

        $request->validate([
            'trainerName' => 'required|string|max:255',
            'gender' => 'required|string',
            'qualification' => 'required|string|max:255',
            'email' => 'required|email:dns|unique:users,email',
            'mobile' => 'required|digits:10|unique:users,phone',
            'designation' => 'required|string',
            'dob' => 'required|date',
            'module_access' => 'required|array|min:1',
            'module_access.*' => 'string'
        ]);

        DB::beginTransaction();

        try {

			$dob = $request->post('dob');
			$password = str_replace('-', '', $dob).'@f365';

	        $user = User::create([
	            'name' => $request->trainerName,
	            'email' => $request->email,
	            'phone' => $request->mobile,                
	            'position' => $request->designation,
	            'userid' => $request->email,
	            'self_registrationId' => $this->generateUserId(),
	            'password' => Hash::make($password), 
	            'role_id' => 8,   
	            'status' => 1, 
	            'created_at' => now(),
	            'updated_at' => now(),
	        ]);

	        $creatorId = Auth::id();
	        $schoolId = DB::table('school_reference')->where('school_user_id', $creatorId)->where('status', 1)->value('school_id');
	        if (!$schoolId) {
	            throw new \Exception("Creator is not linked to any school in school_reference.");
	        }

	        DB::table('school_reference')->insert([
	            'school_id' => $schoolId,
	            'school_user_id' => $user->id,
	            'status' => 1,
	            'created_at' => now(),
	            'updated_at' => now(),
	        ]);

	        UserMeta::updateOrCreate(
	            ['user_id' => $user->id],
	            [
	                'gender' => $request->gender,
	                'dob' => $request->dob,
	                'qualification' => $request->qualification,
	                'address' => $request->address,
	                'module_access' => json_encode($request->module_access),
	                'created_by' => $creatorId,
	                'created_at' => now(),
	                'updated_at' => now(),
	            ]
	        );

	        DB::commit();

			if ($request->ajax()) {
				return response()->json(['status' => 'success', 'message' => 'School Admin added and linked to school successfully!']);
			}
			return redirect()->back()->with('success', 'School Admin added and linked to school successfully!');

        } catch (\Throwable $e) {
	       DB::rollBack();

			if ($request->ajax()) {
				return response()->json(['status' => 'error', 'message' => 'Failed to add School Admin: ' . $e->getMessage()], 500);
			}
			return redirect()->back()->with('error', 'Failed to add School Admin: ' . $e->getMessage());
	    }
    }

    public function handleAdminAction(Request $request) {
	    $request->validate([
	        'users_id' => 'required|integer|exists:users,id',
	        'action' => 'required|string|in:activate,deactivate,delete'
	    ]);

	    $users = User::find($request->users_id);

	    if(!$users) {
	        return response()->json(['success' => false, 'message' => 'School Admin not found']);
	    }

	    switch($request->action) {
	        case 'activate':
	            DB::table('school_reference')->where('school_user_id', $request->users_id)->update(
	            	[  'status' => 1 , 'updated_at' => now()]
			    );
	            $message = 'School Admin activated successfully';
	            break;

	        case 'deactivate':
	            DB::table('school_reference')->where('school_user_id', $request->users_id)->update(
	            	[ 'status' => 0, 'updated_at' => now()]
			    );
	            $message = 'School Admin deactivated successfully';
	            break;

	        case 'delete':

	            DB::table('usermetas')->where('user_id', $request->users_id)->delete();
                DB::table('school_reference')->where('school_user_id', $request->users_id)->delete();

                $users->delete();

	            $message = 'School Admin deleted successfully';
	            break;

	        default:
	            return response()->json(['success' => false, 'message' => 'Invalid action']);
	    }

	    return response()->json(['success' => true, 'message' => $message]);
	}

	public function exportAdminCredentials(Request $request) {

	    $schoolUserIds = $request->input('school_user_ids', []);
	    if(empty($schoolUserIds)) {

	        return response()->json(['message' => 'No School Admin selected'], 400);
	    }

	    $timestamp = Carbon::now()->format('Ymd_His');
		$fileName = "school-admins_{$timestamp}.xlsx";
		return Excel::download(new SchoolAdminCredentials($schoolUserIds), $fileName);

	}

	public function updateAdminDetails(Request $request){

		$userId = $request->viewerId;

		$request->validate([
			'trainerName'    => 'required|string|max:255',
			'gender'         => 'required|string',
			'qualification'  => 'required|string|max:255',
			'email'          => 'required|email|unique:users,email,' . $userId,			
            'mobile' 		 => 'required|digits:10|unique:users,phone,' . $userId,
			'designation'    => 'required|string',
			'dob'            => 'required|date',
			'module_access'  => 'required|array|min:1',
			'module_access.*'=> 'string'
		]);

		DB::table('users')
			->where('id', $userId)
			->update([
				'name'       => $request->trainerName,
				'email'      => $request->email,
				'phone'      => $request->mobile,
				'position'   => $request->designation,
				'updated_at' => now(),
			]);

		DB::table('usermetas')
			->where('user_id', $userId)
			->update([
				'gender'        => $request->gender,
				'qualification' => $request->qualification,
				'dob'           => $request->dob,
				'module_access' => json_encode($request->module_access),
				'updated_at'    => now(),
			]);

		// Return JSON response instead of redirect
		return response()->json([
			'status' => 'success',
			'message' => 'Viewer details updated successfully!',
		]);
	}

	
	public function studentEmail(Request $request){

 		$title = "Parent's Email Update";
		$userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');
		$school = School::find($schoolId);
		
		$classList = $school->getClasses;
		foreach ($classList as $class) {
		    $originalClass = Sclass::where('id', $class->class_id)->orderBy('orders')->first();
		    $class->name = !empty($class->nomenclature) 
		        ? $class->nomenclature 
		        : ($originalClass ? $originalClass->name : null);

		}


		$classList->prepend((object)[
	        'class_id' => '',
	        'name' => 'Select Class',
	        'section' => ''
	    ]);
		$studentsQuery = DB::table('schools')
		->join('students', 'students.school_id', '=' , 'schools.id')
		->leftJoin('students_meta', 'students_meta.student_id', '=', 'students.id')
		->leftJoin('class', 'students.class_id', '=', 'class.id')
    	->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
		->select(
			'students.id as student_id',
			'students.student_name',
			'students.student_uid as admissionnumber',
			'students.class_id',
			'students.section_id',
			'students.rollno',
			'students.custom_class_id',
			'students.email_id',
			DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                 END AS display_classname"),
        	'custom_classes.section',
			)
		->orderBy('students.class_id')
		->orderBy('students.section_id')
		->orderBy('students.rollno')
		->where('students.school_id', (string)$schoolId)
		->where('students.academic_year', '2026-2027')
		->where('students.status', 'active');
		
		// echo"<pre>";print_r($studentsQuery);exit();
		
		if ($request->filled('class_id')) {
			$studentsQuery->where('students.class_id', $request->class_id);
		}

		if ($request->filled('section_id')) {
			$studentsQuery->where('students.section_id', $request->section_id);
		}

		$studentsDetails  = (clone $studentsQuery)->get();
		// dd($studentsDetails);
    	if($request->ajax()){

			return Datatables::of($studentsDetails)
	        ->addIndexColumn()

			->addColumn('checkbox', function($row){
				return '<input type="checkbox" class="row-select" value="'.$row->student_id.'" data-id="'. $row->student_id .'">';
		    })

	        ->addColumn('class_id', function($row) {
	        	return $row->display_classname;
            })

			->addColumn('section_id', function($row){
				return $row->section_id;
            })

			->addColumn('rollno', function($row) {
				return $row->rollno;
			})

            ->rawColumns(['checkbox','class_id','section_id','rollno'])
	        ->toJson();
        }


		$logs = DB::table('email_batch')->where('school_id', $schoolId)->get();
		return view('school.update-students-email', compact(
			'title',
			'logs',
			'classList'
		));
	}

	public function downloadEmailTemplate(Request $request){

		try {
			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');
			$schoolCode = DB::table('schools')->where('id', $schoolId)->value('school_code');

			$timestamp = date('d_Hi');
			$export = new StudentEmailTemplete($schoolId, $request->studentIds);
			$fileName = "{$schoolCode}_{$timestamp}_email_template.xlsx";
			return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName);

		} catch (\Exception $e) {

			\Log::error('Download Email Template Failed: '.$e->getMessage());
			return response()->json([
				'message' => 'Something went wrong while downloading file'
			], 500);
		}
	}

	public function uploadEmailTemplate(Request $request){
		try {

			$request->validate([
				'file' => 'required|mimes:xlsx,xls'
			]);
			
			
			$userId = Auth::user()->id;
			$schoolId = DB::table('school_reference')
			->where('school_user_id', $userId)
			->where('status', 1)
			->value('school_id');

			$file = $request->file('file');

			$import = new class implements \Maatwebsite\Excel\Concerns\ToArray {
				public function array(array $array): void {
					//return $array;
				}
			};


			$data = Excel::toArray($import, $file);

			$excelSchoolId = trim($data[0][0][0] ?? '');

			if ((int)$excelSchoolId !== (int)$schoolId) {
				return response()->json([
					'status' => false,
					'message' => 'This file does not belong to your school.'
				], 422);
			}
			if (empty($data[0]) || !isset($data[0][2])) {
				return response()->json([
					'status' => false,
					'message' => 'Invalid Excel template. Please download the latest template.'
				], 422);
			}

			// Expected headers from StudentEmailTemplete
			$expectedHeaders = [
				'student_id',
				'Class',
				'Section',
				'Student Name',
				'Roll No',
				'Email Id'
			];

			$uploadedHeaders = array_map(function ($header) {
				return strtolower(trim((string) $header));
			}, array_slice($data[0][2], 0, 6));

			$expectedHeaders = array_map('strtolower', $expectedHeaders);

			if ($uploadedHeaders !== $expectedHeaders) {
				return response()->json([
					'status' => false,
					'message' => 'Invalid Excel headers. Please upload the original downloaded email template with all columns in the correct order.'
				], 422);
			}

			$existing = DB::table('email_batch')
			->where('school_id', $schoolId)
			->whereIn('status', ['pending', 'processing'])
			->exists();
			
			if($existing){
				return response()->json([
					'status' => false,
					'message' => 'Parent Email updation in already in precess for your school. Please wait for the completion of your previous request.'
				], 422);
			}

			$rows = $data[0];

			// Remove title row + heading row
			unset($rows[0], $rows[1], $rows[2]);
			$rows = array_values($rows);

			$hasError = false;
			$errorRows = [];
			$cleanRows = [];
			$totalRows = count($rows);

			foreach ($rows as $index => $row) {

				if (!is_array($row)) {
					continue;
				}

				$email = trim($row[5] ?? '');
				$error = '';

				if (empty($email)) {

					$error = 'Email is missing.';

				} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

					$error = "Invalid Email format '{$email}'. Please enter a valid email like example@domain.com.";

				} else {

					$domain = strtolower(substr(strrchr($email, '@'), 1));

					if (!checkdnsrr($domain, 'MX')) {
						$error = "Invalid email domain '{$domain}'. The domain does not have a valid mail server.";
					}
				}

				if (!empty($error)) {

					$excelRowNumber = $index + 4;

					$errorRows[] = $excelRowNumber;
					$hasError = true;

					$cleanRows[] = [
						$row[1] ?? '',
						$row[2] ?? '',
						$row[3] ?? '',
						$row[4] ?? '',
						$email,
						$error
					];
				}
			}

			if ($hasError) {

				$fileName = 'email_errors_' . time() . '.xlsx';

				Excel::store(
					new \App\Exports\StudentEmailErrorExport($cleanRows, $schoolId),
					'email_errors/' . $fileName,
					'emails'
				);
				Session::put('invalid_emails_file', $fileName);

				return response()->json([
					'status' => false,
					'validation_error' => true,
					'totalRows' => $totalRows,
					'error_count' => count($errorRows),
					'download_url' => route('download.email.error')
				]);
			}else{
				$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
				$extension = $file->getClientOriginalExtension();

				$filename = $originalName . '_' . date('His') . '.' . $extension;
				$storedPath = Storage::disk('azure')->putFileAs(
					"import_emails/{$schoolId}",
					$file,
					$filename
				);

				$batchId = DB::table('email_batch')->insertGetId([
					'school_id'      => $schoolId,
					'file_path'      => $storedPath,
					'total_students' => $totalRows,
					'success_count'  => 0,
					'failed_count'   => 0,
					'status'         => 'pending',
					'created_at'     => now(),
					'updated_at'     => now(),
				]);

				ImportStudentEmailJob::dispatch($batchId)->onQueue('import_emails');

				return response()->json([
					'status' => true,
					'message' => 'Emails imported successfully.',
					'batch_id' => $batchId,
				]);
			}
		} catch (\Exception $e) {

			Log::info($e->getMessage());

			return response()->json([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function downloadEmailError(){

		$fileName = Session::get('invalid_emails_file');
		$filePath = 'email_errors/' . $fileName;

		if (!Storage::disk('emails')->exists($filePath)) {
			abort(404, 'No failed import records found.');
		}

		return Storage::disk('emails')->download($filePath, $fileName);
	}

	public function downloadEmailFile($logId) {

        $log = DB::table('email_batch')->where('id', $logId)->first();
		$filePath = $log->file_path; 

	    if (!$filePath || !Storage::disk('azure')->exists($filePath)) {
	        abort(404, 'File not found.');
	    }

	    return Storage::disk('azure')->download( $filePath, basename($filePath),
	        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
	    );
    }

}
