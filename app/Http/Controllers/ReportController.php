<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use PDF;
use DB;
use Auth;
use Session;
use DataTables;
use Carbon\Carbon;
use App\Models\School;
use App\Models\Sclass;
use App\Models\Sstudent;
use App\Traits\ReportHelperTrait;


use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;
use App\Jobs\GenerateReportBatchJob;
use App\Jobs\MergeSchoolReportsJob;
use App\Services\DataTableListService;

use App\Jobs\GenerateSchoolReportsMasterJob;

use App\Models\ReportBatch;
use App\Models\ReportRequest;
use App\Models\EmailReportBatch;
use App\Models\EmailReportRequest;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Cache;
use App\Models\TermMaster;
use Illuminate\Contracts\Encryption\DecryptException;


class ReportController extends Controller {
	
	use ReportHelperTrait;	

	protected $higherClasses;

    public function __construct() {	
   
        $this->middleware('auth:web')->except(['downloadFitnessReport','ViewFitnessReport', 'ViewCWSNFitnessReport', 'downloadCWSNFitnessReport']);
        $this->higherClasses = [4, 5, 6, 7, 8, 9, 10, 11, 12];
        $this->cwsnClasses = [5, 6, 7, 8, 9, 10, 11, 12];
        $this->lowerClass = [1, 2, 3];
    }
	
	private function notEmpty($column) {
		
	    return "COALESCE(NULLIF(TRIM(REPLACE(REPLACE(REPLACE($column, '---', ''), '--', ''), 'N/A', '')), ''), '') <> ''";
	}

	/**
	 * Fitness Test Reports Module
	 * */

	public function FitnessReports(Request $request, DataTableListService $dataTable) {

		$title  = 'Assessment Report';
		$userId = Auth::user()->id;
		$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		$school = School::find($schoolId);
		$TermMasterId = $this->getTermId($schoolId);

		$year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

		$termDetails = $request->input('school-terms');
		if (!empty($termDetails) && str_contains($termDetails, '|')) {
			list($academicYear, $TermMasterId) = explode('|', $termDetails);
		}
		
		$studentsQuery = DB::table('schools')
			->where('students.school_code', $school->school_code)
			->where('students.academic_year', $academicYear)
			// ->where('students.status','!=', 'transfer')
			->join('students', 'students.school_id', '=' , 'schools.id')
			->leftJoin('report_requests', 'report_requests.student_id', '=', 'students.id')			
			->leftJoin('email_report_requests', 'email_report_requests.student_id', '=', 'students.id')

			->leftJoin('class', 'students.class_id', '=', 'class.id')
	    	->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
	    	->leftJoin('SeniorTestResultsSummary as senior', function ($join)  use ($TermMasterId){
	            $join->on('students.id', '=', 'senior.student_id')
	                 ->on('students.school_id', '=', 'senior.school_id')
					 ->where('senior.term_id', '=', $TermMasterId);
	        })
	        ->leftJoin('LowerTestResultsSummary as lower', function ($join)  use ($TermMasterId) {
	            $join->on('students.id', '=', 'lower.student_id')
	                 ->on('students.school_id', '=', 'lower.school_id')
					 ->where('lower.term_id', '=', $TermMasterId);
	        })->leftJoin('CwsnTestResultSummary as cwsn', function ($join)  use ($TermMasterId){
	            $join->on('students.id', '=', 'cwsn.student_id')
	                 ->on('students.school_id', '=', 'cwsn.school_id')
					 ->where('cwsn.term_id', '=', $TermMasterId);
	        })
			->select(
				'schools.id as schools_id',
				'schools.school_code as school_code',
				'students.id',
				'students.student_uid as admission_number',
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
				'custom_classes.orders as class_order',
				'report_requests.status as report_status',
				'email_report_requests.status as email_status',
				DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                 END AS display_classname"), 'custom_classes.section',
			);
			// ->orderBy('custom_classes.orders')->orderby('students.rollno');

	    $statusCase = "
		CASE 
			WHEN students.class_id IN (" . implode(',', $this->cwsnClasses) . ")
     			AND students.is_pwd = '1' THEN
				CASE 
					WHEN (
						CASE WHEN " . $this->notEmpty('cwsn.20m_pacer') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.15m_pacer') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.1mile_run_walk') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.shoulder_stretch') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.sit_and_reach') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.modified_apley_test') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.curlup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.modified_curlup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.dumbbell_press') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.pullup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.pushup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.seated_pushup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.trunk_lift') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.Isometric_pushup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.reverse_curl') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.modified_pullup') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.40m_push_walk') . " THEN 1 ELSE 0 END +
						CASE WHEN " . $this->notEmpty('cwsn.cwsn_bmi') . " THEN 1 ELSE 0 END
					) >= 4
					THEN 'Completed'
					ELSE 'Incomplete'
				END
			WHEN students.class_id IN (" . implode(',', $this->higherClasses) . ") THEN 
		        CASE 
		            WHEN 
		                " . $this->notEmpty('senior.sit_and_reach') . " AND
		                " . $this->notEmpty('senior.run_600m') . " AND
		                " . $this->notEmpty('senior.pushups') . " AND
		                " . $this->notEmpty('senior.dash_50m') . " AND
		                " . $this->notEmpty('senior.curlup') . " AND
		                " . $this->notEmpty('senior.bmi') . " AND
		                " . $this->notEmpty('senior.height') . " AND
		                " . $this->notEmpty('senior.weight') . "
		            THEN 'Completed'
		            ELSE 'Incomplete'
		        END
		    ELSE 
		        CASE 
		            WHEN 
		                " . $this->notEmpty('lower.running') . " AND
		                " . $this->notEmpty('lower.hopping') . " AND
		                " . $this->notEmpty('lower.jumping_landing') . " AND
		                " . $this->notEmpty('lower.skipping') . " AND
		                " . $this->notEmpty('lower.dodging') . " AND
		                " . $this->notEmpty('lower.one_foot_balance') . " AND
		                " . $this->notEmpty('lower.beam_walk') . " AND
		                " . $this->notEmpty('lower.catching_receiving_bounce') . " AND
		                " . $this->notEmpty('lower.catching_small_ball') . " AND
		                " . $this->notEmpty('lower.under_arm_throw') . " AND
		                " . $this->notEmpty('lower.over_arm_throw') . " AND
		                " . $this->notEmpty('lower.striking_drop_hit') . " AND
		                " . $this->notEmpty('lower.dribbling_hands') . " AND
		                " . $this->notEmpty('lower.dribbling_feet') . " AND
		                " . $this->notEmpty('lower.kicking_ball') . " AND
		                " . $this->notEmpty('lower.flamingo_balance') . " AND
		                " . $this->notEmpty('lower.plate_tapping') . " AND
		                " . $this->notEmpty('lower.bmi') . " AND
		                " . $this->notEmpty('lower.height') . " AND
		                " . $this->notEmpty('lower.weight') . "
		            THEN 'Completed'
		            ELSE 'Incomplete'
		        END
		END";

	    $studentsQuery->addSelect(DB::raw("$statusCase AS test_status"));


		$filters = [

			'class' => function ($studentsQuery, $value) {
                 $studentsQuery->where('students.class_id', $value);
            },

            'section' => function ($studentsQuery, $value) {
                 $studentsQuery->where('students.section_id', $value);
            },

            'class-section' => function ($studentsQuery, $value) {
                list($class_id, $section_id) = array_pad(explode('-', $value), 2, null);

                if (!empty($class_id)) {
                    $studentsQuery->where('students.class_id', $class_id);
                }

                if (!empty($section_id)) {
                    $studentsQuery->where('students.section_id', $section_id);
                }
            },

            'test-status' => function ($studentsQuery, $value) {            	
	           if ($value === 'complete') {
			        $studentsQuery->having('test_status', '=', 'Completed');
			    } elseif ($value === 'incomplete') {
			        $studentsQuery->having('test_status', '=', 'Incomplete');
			    }
	        },

            'student-category' => function ($studentsQuery, $value) {            	
	           if ($value === 'normal') {
			        $studentsQuery->where('is_pwd', '=', '0');
			    } elseif ($value === 'cwsn') {
			        $studentsQuery->where('is_pwd', '=', '1');
			    }
	        },

            'status' => function ($studentsQuery, $value) {            	
	           $studentsQuery->where('students.status', $value);
	        }
        ];

		if ($request->ajax()) {

		    return $dataTable
	        ->setQuery($studentsQuery)
	        ->setFilters($filters)
	        ->setSearchableColumns(['student_name', 'student_uid'])
           	->setSortableColumns([
			    'student_name' => 'student_name',
			    'admission_number' => 'admission_number',
			    'rollno' => 'rollno',
				'class_order' => 'class_order',
			    'section_id' => 'section_id',
			    'gender' => 'gender'
			])

			->addCustomColumn('student_name', function($row) {
				if($row->is_pwd == 1){
					return $row->student_name . ' <span style="color:white; background:#e74c3c; padding:2px 6px; border-radius:4px; font-size:10px;">CWSN</span>';
				} else {
					return $row->student_name;
				}
			})
			
            ->addCustomColumn('testStatus', function ($row) {
                $status = $row->test_status ?? 'Incomplete';
                if ($status === 'Completed') {
                    return '<span class="badge bg-success text-light p-2" style="font-size:14px;" >Completed</span>';
                }
                return '<span class="badge text-light p-2 btn-primary" style="font-size:14px;">Incomplete</span>';
            })
			->addCustomColumn('viewReport', function ($row) use($TermMasterId) {
                $encryptedId = Crypt::encryptString($row->id);
				if($row->is_pwd === '1' && $row->class_id >= 5){
					$url = route('cwsn.reports.view', ['id' => $encryptedId, 'term_id' => $TermMasterId]);
				}else{
					$url = route('reports.view', ['id' => $encryptedId, 'term_id' => $TermMasterId]);
				}
				
                return '<a href="' . $url . '" class="btn-link mr-1" target="_blank">View</a>';
            })

            ->addCustomColumn('downloadReport', function ($row) use($TermMasterId) {
                $encryptedId = Crypt::encryptString($row->id);
				if($row->is_pwd === '1' && $row->class_id >= 5){
					$url = route('cwsn.reports.download', ['id' => $encryptedId, 'term_id' => $TermMasterId]);
				}else{
					$url = route('reports.download', ['id' => $encryptedId, 'term_id' => $TermMasterId]);
				}
				
                return '<a type="button" href="' . $url . '" class="btn btn-primary btn-sm text-light w-40"	>
                <i class="fa-solid fa-download"></i> </a>';
            })

            ->render($request);
	    }

	    return view('reports.fitnessreports',compact('title','TermMasterId'));	
	}

    public function HigherClassTestSummary(Request $request)  {

        $userId = Auth::user()->id;
      	$role_id =  \Auth::user()->role_id;

        if($role_id == 3){

        	if(Session::get('SelectSchoolId')) {
				$schoolId = Session::get('SelectSchoolId');
			}else{
				$schoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
        }else{
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		}

		$TermMasterId = $this->getTermId($schoolId);

		$TermIds = $this->getCurrentAndPreviousTermIds($schoolId, (int) $TermMasterId);
		
		$terms = TermMaster::whereIn('id', $TermIds)->get();
		
        $ajaxUrl = route('higherclasstestsummary');

        $school = School::find($schoolId);
		$higherClass = $this->higherClasses;

		$classList = $school->getClasses
			->filter(function ($class) use ($higherClass) {
		        return in_array($class->class_id, $higherClass);
		    })
			->map(function ($class) {
	        $originalClass = Sclass::where('id', $class->class_id)
	            ->orderBy('orders')
	            ->first();

	        $class->name = !empty($class->nomenclature)
	            ? $class->nomenclature
	            : ($originalClass ? $originalClass->name : null);

	        return $class;
	    });

		$classList->prepend((object)[
	        'class_id' => '',
	        'name' => 'All Class',
	        'section' => ''
	    ]);



        if ($request->ajax()) {

            $start = $request->input('start', 0);
            $length = $request->input('length', 100);

			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;

			if ($request->input('term')) {
		        $termDetails = $request->input('term');
				list($academicYear, $selectedTerm) = explode('|', $termDetails);
		        if (!empty($selectedTerm)) {
					$TermMasterId = $selectedTerm;
		        }
		    }
			
		
            $query = DB::table('students as s')
		        ->leftJoin('SeniorTestResultsSummary as r', function($join) use ($TermMasterId) {
					$join->on('s.id', '=', 'r.student_id')
						->where('r.term_id', '=', $TermMasterId);
				})
		        ->leftJoin('class', 's.class_id', '=', 'class.id')
				->leftJoin('custom_classes', 's.custom_class_id', '=', 'custom_classes.id')
			        ->select( 's.id','s.is_pwd', 's.dob', 's.student_name', 's.rollno' ,'r.sit_and_reach', 'r.run_600m', 'r.pushups', 'r.dash_50m',  'r.curlup',  'r.bmi', 'r.height', 'r.weight', 's.class_id','s.section_id',
			             DB::raw("CASE 
							WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
							THEN custom_classes.nomenclature 
							ELSE class.name 
						 END AS display_classname"),
					'custom_classes.section'
	            )
		        ->where('s.school_code', $school->school_code)
				->where('s.academic_year', $academicYear)    
				->whereNotIn('s.status', ['transfer', 'deactivated'])
				->where(function ($query) {
					$query->where('class.id', '=', 4)
						->orWhere(function ($q) {
							$q->where('class.id', '>', 4)
								->where('s.is_pwd', '!=', '1');
						});
				})  
			    ->whereIn('class.id', $higherClass)							
				->orderBy('class.id')
				->orderBy('custom_classes.section')
				->orderBy('s.rollno');	
		      

			if ($request->input('class')) {
		        $classFilter = $request->input('class');

		        list($class_id, $section_id) = explode('-', $classFilter, 2);
		        if (!empty($class_id)) {
		            $query->where('s.class_id', $class_id);
		        }
		        if (!empty($section_id)) {
		            $query->where('s.section_id', $section_id);
		        }
		    }


		    $status = $request->input('status');
			$tests  = $request->input('test', []);
		    $allTests = ['sit_and_reach','run_600m','pushups','dash_50m','curlup','bmi','flamingo','plate_tapping'];

			if (!empty($request->status)) {
				if ($request->status === 'pwd') {
					$query->where('s.is_pwd', '1');
				}
				if (in_array($request->status, ['complete', 'incomplete']) && !empty($tests)) {

					if (in_array('all', $tests)) {
						$tests = $allTests;
					}

					$query->where(function ($q) use ($tests, $request) {

						if ($request->status === 'complete') {
							foreach ($tests as $test) {
								$q->whereNotNull("r.$test");
							}
						}

						if ($request->status === 'incomplete') {
							foreach ($tests as $test) {
								$q->orWhereNull("r.$test");
							}
						}

					});
				}
			}

	        $searchValue = $request->input('search.value');
	        if (!empty($searchValue)) {
			    $query->where(function ($q) use ($searchValue) {
			        $q->orWhere('s.student_name', 'LIKE', "%{$searchValue}%")
			          ->orWhere('s.rollno', 'LIKE', "%{$searchValue}%");
			    });
			}


	        if ($request->has('order')) {
	            $order = $request->order[0];
	            $colIndex = $order['column'];
	            $direction = $order['dir']; // asc | desc
	            $colName = $request->columns[$colIndex]['data'];

	            $sortableColumns = [
	                'class_name'     => DB::raw("display_classname"),
					'rollno'   		 => 's.rollno',
	                'student_name'   => 's.student_name',
					'age'   		 => 's.dob',
	                'sit_and_reach'  => 'r.sit_and_reach',
	                'run_600m'       => 'r.run_600m',
	                'pushups'        => 'r.pushups',
	                'dash_50m'       => 'r.dash_50m',
	                'curlup'         => 'r.curlup',
	                'bmi'            => 'r.bmi',
	                'height'         => 'r.height',
	                'weight'         => 'r.weight',
	            ];

	            if (array_key_exists($colName, $sortableColumns)) {
	                $query->orderBy($sortableColumns[$colName], $direction);
	            }
	        }

            $recordsTotal = $query->count();
        	$draw = intval($request->input('draw'));
        	$studentlist = $query->skip($start)
            ->take($length == -1 ? $recordsTotal : $length)->get();
	
	        $flattenedList = $studentlist->map(function ($item) use ($role_id, $TermMasterId) {
				$id  = Crypt::encryptString($item->id);
           		$view_link = route('reports.view', ['id' => $id, 'term_id' => $TermMasterId]);

	        	// $view_link = route('reports.view', ['id' => Crypt::encryptString($item->id)]);

				$age = \Carbon\Carbon::parse($item->dob)->age;
				$isValid = $this->isValidAge($item->class_id, $age);

				$studentAge = $isValid ? $age : $age;

	            return [
	                'id' => $item->id,
					'student_name' => $item->student_name . ($item->is_pwd == 1 
															? ' <span style="color:white; background:#e74c3c; padding:2px 6px; border-radius:4px; font-size:10px;">PWD</span>' 
															: ''),					
					'age'			=> $studentAge,
					'invalid_age' => $isValid ? 0 : 1, 
	           		'class_name' => $item->display_classname.'-'. $item->section,
	           		'rollno' => $item->rollno ?? 'N.A.',
	                'sit_and_reach' => isset($item->sit_and_reach) ? (int)$item->sit_and_reach / 10 : '---',
	                'run_600m' => is_numeric($item->run_600m)
									? sprintf(
										'%02d:%02d.%02d',
										floor((int)$item->run_600m / 60000),
										floor(((int)$item->run_600m % 60000) / 1000),
										floor(((int)$item->run_600m % 1000) / 10)
									)
									: '---',
	                'pushups' => isset($item->pushups) ? (int)$item->pushups : '---',
	                'dash_50m' => is_numeric($item->dash_50m)
									? sprintf(
										'%02d:%02d.%02d',
										floor((int)$item->dash_50m / 60000),
										floor(((int)$item->dash_50m % 60000) / 1000),
										floor(((int)$item->dash_50m % 1000) / 10)
									)
									: '---',
	                'curlup' => isset($item->curlup) ? (int)$item->curlup : '---',
	                'bmi' => isset($item->bmi) ? round((float)$item->bmi, 2) : '---',
	                'height' => $item->height ?? '---',
	                'weight' => $item->weight ?? '---',	                
	                'view_link' => $view_link,
	            ];
	        });

            return response()->json([
	            'draw' => intval($draw),
	            'recordsTotal' => $recordsTotal,
	            'recordsFiltered' => $recordsTotal,
	            'data' => $flattenedList
	        ]);
        }

       	$title = 'Fitness Test Status (Class 4 to 12)';
        return view('reports.summary.higherclass', compact('title','classList','ajaxUrl','terms','TermMasterId'));
    }

    /**
     * Lower Class Test Summary
     * */
    public function LowerClassTestSummary(Request $request) {

	    $userId  = Auth::user()->id;
	    $role_id = Auth::user()->role_id;

	    if($role_id == 3){

        	if(Session::get('SelectSchoolId')) {
				$schoolId = Session::get('SelectSchoolId');
			}else{
				$schoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
        }else{
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		}

		$TermMasterId = $this->getTermId($schoolId);

		$TermIds = $this->getCurrentAndPreviousTermIds($schoolId, (int) $TermMasterId);
		
		$terms = TermMaster::whereIn('id', $TermIds)->get();

	    $ajaxUrl = route('lowerclasstestsummary');


	    $school = School::find($schoolId);
	    $lowerClass = $this->lowerClass;


	    $classList = DB::table('custom_classes as cc')
        ->leftJoin('class as c', 'c.id', '=', 'cc.class_id')
        ->where('cc.school_id', $schoolId)
        ->whereIn('cc.class_id', $lowerClass)
        ->select(
            'cc.class_id',
            DB::raw("CASE 
                WHEN cc.nomenclature IS NOT NULL AND cc.nomenclature <> '' 
                THEN cc.nomenclature 
                ELSE c.name 
             END as name"),
            'cc.section'
        )
        ->orderBy('c.orders')
        ->get();

	    $classList->prepend((object)[
	        'class_id' => '',
	        'name'     => 'All Class',
	        'section'  => ''
	    ]);

	    if ($request->ajax()) {
	        $start  = $request->input('start', 0);
	        $length = $request->input('length', 100);
	        $draw   = intval($request->input('draw'));	

			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;

			if ($request->input('term')) {
		        $termDetails = $request->input('term');
				list($academicYear, $selectedTerm) = explode('|', $termDetails);
		        if (!empty($selectedTerm)) {
					$TermMasterId = $selectedTerm;
		        }
		    }

	        $query = DB::table('students as s')
	            ->leftJoin('LowerTestResultsSummary as r', function($join) use ($TermMasterId) {
					$join->on('s.id', '=', 'r.student_id')
						->where('r.term_id', '=', $TermMasterId);
				})
	            ->leftJoin('class', 's.class_id', '=', 'class.id')
	            ->leftJoin('custom_classes', 's.custom_class_id', '=', 'custom_classes.id')
	            ->select(
	                's.id',
					's.dob',
	                's.student_name',
					's.rollno',
	                's.class_id',
	                's.section_id',
					's.is_pwd',
	                'r.running', 'r.hopping', 'r.jumping_landing', 'r.one_foot_balance',
	                'r.skipping', 'r.dodging', 'r.beam_walk', 'r.catching_receiving_bounce',
	                'r.catching_small_ball', 'r.under_arm_throw', 'r.over_arm_throw',
	                'r.striking_drop_hit', 'r.dribbling_hands', 'r.dribbling_feet',
	                'r.kicking_ball', 'r.flamingo_balance', 'r.plate_tapping',
	                'r.bmi', 'r.height', 'r.weight',
	                DB::raw("CASE 
	                            WHEN custom_classes.nomenclature IS NOT NULL 
	                                 AND custom_classes.nomenclature <> '' 
	                            THEN custom_classes.nomenclature 
	                            ELSE class.name 
	                         END AS display_classname"),
	                'custom_classes.section'
	            )				
	            ->where('s.school_code', $school->school_code)
				->where('s.academic_year', $academicYear)					
				->whereNotIn('s.status', ['transfer', 'deactivated']) 
	            ->whereIn('class.id', $lowerClass)				
				->orderBy('class.id')
				->orderBy('custom_classes.section')
				->orderBy('s.rollno');

	        if ($request->filled('class')) {
	            [$class_id, $section_id] = explode('-', $request->input('class'));
	            if (!empty($class_id)) {
	                $query->where('s.class_id', $class_id);
	            }
	            if (!empty($section_id)) {
	                $query->where('s.section_id', $section_id);
	            }
	        }


	        $tests      = $request->input('test', []);
	        $status     = $request->input('status');
	        $allTests   = [
	            'running', 'hopping', 'jumping_landing', 'one_foot_balance', 'skipping',
	            'dodging', 'beam_walk', 'catching_receiving_bounce', 'catching_small_ball',
	            'under_arm_throw', 'over_arm_throw', 'striking_drop_hit', 'dribbling_hands',
	            'dribbling_feet', 'kicking_ball', 'flamingo_balance', 'plate_tapping', 'bmi'
	        ];

	        if (!empty($request->status)) {
				if ($request->status === 'pwd') {
					$query->where('s.is_pwd', '1');
				}
				if (in_array($request->status, ['complete', 'incomplete']) && !empty($tests)) {

					if (in_array('all', $tests)) {
						$tests = $allTests;
					}

					$query->where(function ($q) use ($tests, $request) {

						if ($request->status === 'complete') {
							foreach ($tests as $test) {
								$q->whereNotNull("r.$test");
							}
						}

						if ($request->status === 'incomplete') {
							foreach ($tests as $test) {
								$q->orWhereNull("r.$test");
							}
						}

					});
				}
			}


	        $searchValue = $request->input('search.value');
	        if (!empty($searchValue)) {
			    $query->where(function ($q) use ($searchValue) {
			        $q->orWhere('s.student_name', 'LIKE', "%{$searchValue}%")
			          ->orWhere('s.rollno', 'LIKE', "%{$searchValue}%");
			    });
			}


	        $columns = [
	            0 => 'display_classname',
	            1=>  's.rollno',
	            2 => 's.student_name',
	            3 => 'r.running',
	            4 => 'r.hopping',
	            5 => 'r.jumping_landing',
	            6 => 'r.skipping',
	            7 => 'r.dodging',
	            8 => 'r.one_foot_balance',
	            9 => 'r.beam_walk',
	            10 => 'r.catching_receiving_bounce',
	            11 => 'r.catching_small_ball',
	            12 => 'r.under_arm_throw',
	            13 => 'r.over_arm_throw',
	            14 => 'r.striking_drop_hit',
	            15 => 'r.dribbling_hands',
	            16 => 'r.dribbling_feet',
	            17 => 'r.kicking_ball',
	            18 => 'r.flamingo_balance',
	            19 => 'r.plate_tapping',
	            20 => 'r.bmi',
	            21 => 'r.height',
	            22 => 'r.weight',
				23 => 's.dob'
	        ];

	        if ($request->has('order.0.column')) {
	            $colIndex = $request->input('order.0.column');
	            $colDir   = $request->input('order.0.dir');

	            if (isset($columns[$colIndex])) {
	                $query->orderBy($columns[$colIndex], $colDir);
	            }
	        } else {
	            $query->orderBy('s.student_name', 'asc');
	        }

	        $recordsTotal = $query->count();

	        $draw = intval($request->input('draw'));
	        $studentlist = $query->skip($start)->take($length == -1 ? $recordsTotal : $length)->get();

	        $flattenedList = $studentlist->map(function ($item) use ($role_id, $TermMasterId) {

	           /* $view_link = $role_id == 3
	                ? route('trainer.reports.view', ['id' => Crypt::encryptString($item->id)])
	                : route('reports.view', ['id' => Crypt::encryptString($item->id)]);*/
	            
				$id  = Crypt::encryptString($item->id);
           		$view_link = route('reports.view', ['id' => $id, 'term_id' => $TermMasterId]);
				
				$age = \Carbon\Carbon::parse($item->dob)->age;

				$isValid = $this->isValidAge($item->class_id, $age);

				$studentAge = $isValid ? $age : $age;

	            return [
	                'id'            => $item->id,
	                'student_name' => $item->student_name . ($item->is_pwd == 1 
															? ' <span style="color:white; background:#e74c3c; padding:2px 6px; border-radius:4px; font-size:10px;">PWD</span>' 
															: ''),
					'age'			=> $studentAge,
					'invalid_age' => $isValid ? 0 : 1, 
	                'class_name'    => $item->display_classname . '-' . $item->section,
	                'rollno'        => $item->rollno ?? 'N.A.',
	                'running'       => $item->running ?? '---',
	                'hopping'       => $item->hopping ?? '---',
	                'jumping_landing' => $item->jumping_landing ?? '---',
	                'one_foot_balance' => $item->one_foot_balance ?? '---',
	                'skipping'      => $item->skipping ?? '---',
	                'dodging'       => $item->dodging ?? '---',
	                'beam_walk'     => $item->beam_walk ?? '---',
	                'catching_receiving_bounce' => $item->catching_receiving_bounce ?? '---',
	                'catching_small_ball' => $item->catching_small_ball ?? '---',
	                'under_arm_throw' => $item->under_arm_throw ?? '---',
	                'over_arm_throw'  => $item->over_arm_throw ?? '---',
	                'striking_drop_hit' => $item->striking_drop_hit ?? '---',
	                'dribbling_hands' => $item->dribbling_hands ?? '---',
	                'dribbling_feet'  => $item->dribbling_feet ?? '---',
	                'kicking_ball'    => $item->kicking_ball ?? '---',
	                'flamingo_balance' => !isset($item->flamingo_balance) 
											? '---' 
											: ((int)$item->flamingo_balance === 1000 
												? 'Disqualified' 
												: (int)$item->flamingo_balance),
	                'plate_tapping'   => is_numeric($item->plate_tapping)
										? sprintf(
											'%02d:%02d.%02d',
											floor((int)$item->plate_tapping / 60000),
											floor(((int)$item->plate_tapping % 60000) / 1000),
											floor(((int)$item->plate_tapping % 1000) / 10)
										)
										: '---',
	                'bmi' => isset($item->bmi) ? round((float)$item->bmi, 2) : '---',
	                'height'          => $item->height ?? '---',
	                'weight'          => $item->weight ?? '---',
	                'view_link'       => $view_link,
	            ];
	        });

	        return response()->json([
	            'draw'            => $draw,
	            'recordsTotal'    => $recordsTotal,
	            'recordsFiltered' => $recordsTotal,
	            'data'            => $flattenedList
	        ]);
	    }

	    $title = 'Fitness Test Status (Class 1 to 3)';
	    return view('reports.summary.lowerclass', compact('title', 'classList', 'ajaxUrl','terms','TermMasterId'));
	}

	public function ViewFitnessReport($id=null, $term_id=null)	 {
		
		try{

			if($id){

			    $studentId = Crypt::decryptString($id);
			
			} else {
			    $user = Auth::guard('sstudent')->user();
			    if (!$user) {
			        return redirect()->back()->with('error', 'You must be logged in to view this report.');
			    }

			    $studentId = $user->id;
			}

			$studentsData = $this->getStudentData($studentId);

			if (!empty($term_id)) {
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $term_id);
			} else {
				$selectedTermId = $this->getTermId($studentsData->schools_id);

				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $selectedTermId);
			}

			$currentTermId  = $termIds[0] ?? null;
			$previousTermId = $termIds[1] ?? null;

			$academicYear = DB::table('term_masters')->where('id', $currentTermId)->value('academic_year');
			
			$dob          = Carbon::parse($studentsData->dob);
			$studentAge   = $dob->age;
			$studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
			$ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));

			// Fetch report + benchmarks
			$reportData = $this->getReportData($studentId, $termIds);
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

			if (in_array($studentsData->class_id, $this->higherClasses)) {

				[$orderedReportData, $getFitnessBenchmark] = $this->getSeniorReportData($studentId, $studentAge, $studentGender, $groupedReport);
				return view('reports.fitness.html.senior-report', compact('studentsData','orderedReportData','getFitnessBenchmark','getBmiBenchmark','academicYear'
				));

			} else {
				[$orderedReportData, $FmsReportData, $getFitnessBenchmark] =
				$this->getJuniorReportData($studentsData->class_id, $studentId, $studentAge, $studentGender, $groupedReport, $termIds);
				return view('reports.fitness.html.junior-report', compact('studentsData','orderedReportData','FmsReportData','getFitnessBenchmark','getBmiBenchmark','academicYear'));

			}
		}

		catch (DecryptException $e) {

	        Log::warning('Fitness report link expired or invalid.', [
	            'encrypted_id' => $id,
	            'file' => $e->getFile(),
	            'line' => $e->getLine(),
	        ]);

	        return redirect()->back()->with( 'error', 'This report link has expired. Please log in again to continue.' );

	    }

		 catch (\Exception $e) {
	      
	        Log::error("Error Viewing fitness report: " . $e->getMessage(), [
	            'student_id' => $id ?? 'Auth User',
	            'file' => $e->getFile(),
	            'line' => $e->getLine(),
	            'trace' => $e->getTraceAsString()
	        ]);

	        return redirect()->back()->with('error', 'An error occurred while Viewing the report. Please try again later.');
	    }
	}



	/**
	 * 
	 * Handling Report Card Generation.
	 * */

	public function CheckReportAvailablity() {

		$userId = Auth::id();
        $schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');
	    $reports = DB::table('report_batches')
	    	->where('school_id', $schoolId)
	    	->select('total_students','completed_students','status','download_path','created_at','expires_at')->orderBy('created_at','desc')
	    	->get();

	    if ($reports->isEmpty()) {
	        return response()->json([
	            'html' => '<p class="text-center text-muted">No report requests found.</p>'
	        ]);
	    }

	    $html = view('reports.modals.available-report-cards', compact('reports'))->render();
	    return response()->json(['html' => $html]);
	}

	public function queueBulkReportCards(Request $request) {

		try {
            
            $userId = Auth::id();
            $schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');

            if (!$schoolId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No school found for the current user.'
                ], 400);
            }

            $studentIds = $request->input('student_ids', []);
			$termDetails = $request->input('termId');
			$action = $request->input('action');
			list($academicYear, $termId) = explode('|', $termDetails);

            if (empty($studentIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No student IDs provided.'
                ], 400);
            }
        
            $report_batch = ReportBatch::create([
			    'school_id' => $schoolId,
			    'status' => 'pending',
			]);

            foreach ($studentIds as $studentId) {            	
			    ReportRequest::updateOrCreate(
			        ['student_id' => $studentId],
			        [
			            'school_id' => $schoolId,
			            'batch_id' => $report_batch->id,
			            'status' => 'requested',
			            'requested_at' => now(),
			        ]
			    );
			}			

            GenerateSchoolReportsMasterJob::dispatch($schoolId, $studentIds, $termId, $report_batch->id, $action)->onQueue('report_generation');
            return response()->json([
                'status' => 'queued',
                'message' => 'Your report card request has been submitted and is being processed. Please return to this page later. Once the report is ready, it will appear under Available Downloads.'
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to queue report generation", ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while starting report generation.'
            ], 500);
        }
	}


	public function requestDownload(Request $request, $batchId) {

        $batch = ReportBatch::findOrFail($batchId);
        $schoolId = DB::table('school_reference')->where('school_user_id', Auth::id())
        ->where('status', 1)->value('school_id');
       
        if ($schoolId !== $batch->school_id) {
            abort(403, 'Unauthorized access to this report');
        }

        $signedUrl = URL::temporarySignedRoute(
            'report.download.signed',
            now()->addMinutes(15),
            ['batchId' => $batchId]
        );

        return redirect($signedUrl);
    }
    

    public function downloadSigned(Request $request, $batchId) {

        if (! $request->hasValidSignature()) {
            abort(403, 'This download link has expired or is invalid.');
        }

        $batch = ReportBatch::findOrFail($batchId);
        $schoolId = DB::table('school_reference')->where('school_user_id', Auth::id())
        ->where('status', 1)->value('school_id');

        if ($schoolId !== $batch->school_id) {
            abort(403, 'Unauthorized access to this report');
        }

        if ($batch->expires_at && now()->greaterThan($batch->expires_at)) {
	        abort(410, 'This report has expired and is no longer available.');
	    }


	    $relativePath = 'excel_storage/reports/' . $batch->final_zip_path;
        $disk = Storage::disk('azure');

        
        // $relativePath = $batch->final_zip_path;
        // $disk = Storage::disk('reports');

        if (! $relativePath || ! $disk->exists($relativePath)) {
	        abort(404, 'Report file not found.');
	    }

        $generatedAt = $batch->generated_at ? \Carbon\Carbon::parse($batch->generated_at) : \Carbon\Carbon::parse($batch->created_at);
	    $downloadFileName = sprintf('Fitness_Report_Cards_%s.zip', $generatedAt->format('d-m-Y_H-i-s'));

        return $disk->download($relativePath, $downloadFileName);
    }

	private function isValidAge($class, $age){

		$classDetails = DB::table('class')->where('id', $class)->first();

		if (!$classDetails) {
			return false; 
		}

		$minAge = $classDetails->min_age;
		$maxAge = $classDetails->max_age;

		return ($age >= $minAge && $age <= $maxAge);
	}

		/**
	 * 
	 * download indivisual fitness report for schools/teachers/students
	 * 
	 **/

	/**
	 *  Info: Need to check Exported Reports Name Should Be The Name of the Students.
	 *  */
	public function downloadFitnessReport($id = null, $term_id= null){

		try {			
			if($id){
			    $studentId = Crypt::decryptString($id);
			} else {
			    $user = Auth::guard('sstudent')->user();
			    if (!$user) {
			        return redirect()->back()->with('error', 'You must be logged in to view this report.');
			    }

			    $studentId = $user->id;
			}

			$studentsData = $this->getStudentData($studentId);

			if (!empty($term_id)) {
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $term_id);
			} else {
				$selectedTermId = $this->getTermId($studentsData->schools_id);

				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $selectedTermId);
			}

			$currentTermId  = $termIds[0] ?? null;
			$previousTermId = $termIds[1] ?? null;

			$academicYear = DB::table('term_masters')->where('id', $currentTermId)->value('academic_year');
			
			$dob          = Carbon::parse($studentsData->dob);
			$studentAge   = $dob->age;
			$studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
			$ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));

			// Fetch report + benchmarks
			$reportData = $this->getReportData($studentId, $termIds);
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

			
	        if (in_array($studentsData->class_id, $this->higherClasses)) {
				
				
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

			return $pdf->stream('fitness_report.pdf');
			return $pdf->download('fitness_report.pdf');

		} catch (\Exception $e) {
	      
	        Log::error("Error generating fitness report: " . $e->getMessage(), [
	            'student_id' => $id ?? 'Auth User',
	            'file' => $e->getFile(),
	            'line' => $e->getLine(),
	            'trace' => $e->getTraceAsString()
	        ]);

	        return redirect()->back()->with('error', 'An error occurred while generating the report. Please try again later.');
	    }
    }

	// sent bulk report cards via email
	public function sentBulkReportCards(Request $request) {

		try {
            $userId = Auth::id();
            $schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');

            if (!$schoolId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No school found for the current user.'
                ], 400);
            }

            $studentIds = $request->input('student_ids', []);
			$termDetails = $request->input('termId');
			$action = $request->input('action');
			list($academicYear, $termId) = explode('|', $termDetails);

            if (empty($studentIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No student IDs provided.'
                ], 400);
            }
        
            $report_batch = EmailReportBatch::create([
			    'school_id' => $schoolId,
			    'status' => 'pending',
			]);

            foreach ($studentIds as $studentId) {            	
			    EmailReportRequest::updateOrCreate(
			        ['student_id' => $studentId],
			        [
			            'school_id' => $schoolId,
			            'batch_id' => $report_batch->id,
			            'status' => 'requested',
			            'requested_at' => now(),
			        ]
			    );
			}			

            GenerateSchoolReportsMasterJob::dispatch($schoolId, $studentIds, $termId, $report_batch->id, $action)->onQueue('report_generation');
            return response()->json([
                'status' => 'queued',
                'message' => 'Your request for sending report cards has been submitted and is being processed. Please see the history for details.'
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to queue report generation", ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while starting report generation.'
            ], 500);
        }
	}
	// email report status 

	public function CheckSentStatus(Request $request) {
		if($request->school_id){
			$schoolId = $request->school_id;
		}else{
			$userId = Auth::id();
			$schoolId = DB::table('school_reference')->where('school_user_id', $userId)->where('status', 1)->value('school_id');
		}
	    $reports = DB::table('email_report_batches')
	    	->where('school_id', $schoolId)
	    	->select('total_students','completed_students','status','created_at')->orderBy('created_at','desc')
	    	->get();

		// echo"<pre>";print_r($reports);exit();

	    if ($reports->isEmpty()) {
	        return response()->json([
	            'html' => '<p class="text-center text-muted">No report requests found.</p>'
	        ]);
	    }

	    $html = view('reports.modals.sent-report-status', compact('reports'))->render();
	    return response()->json(['html' => $html]);
	}


	/*
	* CWSN Test Summary
	* 10-Aug-2026
	*/

	public function cwsnTestSummary(Request $request)  {

        $userId = Auth::user()->id;
      	$role_id =  \Auth::user()->role_id;
        if($role_id == 3){

        	if(Session::get('SelectSchoolId')) {
				$schoolId = Session::get('SelectSchoolId');
			}else{
				$schoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$userId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
        }else{
			$schoolId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id');
		}

		$TermMasterId = $this->getTermId($schoolId);

		$TermIds = $this->getCurrentAndPreviousTermIds($schoolId, (int) $TermMasterId);
		
		$terms = TermMaster::whereIn('id', $TermIds)->get();
		
        $ajaxUrl = route('trainer.cwsn.status');

        $school = School::find($schoolId);

		$cwsnClassess = [5, 6, 7, 8, 9, 10, 11, 12];
		$classList = $school->getClasses
			->filter(function ($class) use ($cwsnClassess) {
		        return in_array($class->class_id, $cwsnClassess);
		    })
			->map(function ($class) {
	        $originalClass = Sclass::where('id', $class->class_id)
	            ->orderBy('orders')
	            ->first();

	        $class->name = !empty($class->nomenclature)
	            ? $class->nomenclature
	            : ($originalClass ? $originalClass->name : null);

	        return $class;
	    });

		$classList->prepend((object)[
	        'class_id' => '',
	        'name' => 'All Class',
	        'section' => ''
	    ]);

        if ($request->ajax()) {

            $start = $request->input('start', 0);
            $length = $request->input('length', 100);

			$year = date('Y');
			$month = date('m');
			$academicYear = ($month >= 4)
				? $year . '-' . ($year + 1)
				: ($year - 1) . '-' . $year;

			if ($request->input('term')) {
		        $termDetails = $request->input('term');
				list($academicYear, $selectedTerm) = explode('|', $termDetails);
		        if (!empty($selectedTerm)) {
					$TermMasterId = $selectedTerm;
		        }
		    }
			
		
            $query = DB::table('students as s')
				->leftJoin('student_rpwd_mapping', 'student_rpwd_mapping.student_id', '=', 's.id' )
		        ->leftJoin('CwsnTestResultSummary as r', function($join) use ($TermMasterId) {
					$join->on('s.id', '=', 'r.student_id')
						->where('r.term_id', '=', $TermMasterId);
				})
				->leftJoin('TestResultAdditionalData','TestResultAdditionalData.StudentId' ,'=', 'r.student_id')
		        ->leftJoin('class', 's.class_id', '=', 'class.id')
				->leftJoin('custom_classes', 's.custom_class_id', '=', 'custom_classes.id')
			        ->select( 
					's.id',
					's.student_name',					
					's.class_id',
					's.section_id',
					's.rollno',
					's.dob',
					's.is_pwd',
					'student_rpwd_mapping.pwd_type_id',
					'r.20m_pacer as twenty_m_pacer',
					'r.15m_pacer as fifteen_m_pacer',
					'r.1mile_run_walk as oneMile_run_walk',
					'r.shoulder_stretch', 
					'r.sit_and_reach', 
					'r.modified_apley_test', 
					'r.curlup', 
					'r.modified_curlup', 
					'r.dumbbell_press', 
					'r.pullup', 
					'r.pushup', 
					'r.seated_pushup', 
					'r.trunk_lift', 
					'r.Isometric_pushup', 
					'r.reverse_curl', 
					'r.modified_pullup', 
					'r.40m_push_walk as push_walk',  
					'r.cwsn_bmi',
					'r.height',
					'r.weight',
			             DB::raw("CASE 
							WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
							THEN custom_classes.nomenclature 
							ELSE class.name 
						 END AS display_classname"),
				    'TestResultAdditionalData.TimeTaken',
					'custom_classes.section'
	            )
		        ->where('s.school_code', $school->school_code)
				->where('s.academic_year', $academicYear)    
				->where('s.status','!=', 'transfer')    
				->where('s.is_pwd', '1')    
			    ->whereIn('class.id', $cwsnClassess)							
				->orderBy('class.id')
				->orderBy('custom_classes.section')
				->orderBy('s.rollno');	
		      
				// echo"<pre>";print_r($$query->get());exit();

			if ($request->input('class')) {
		        $classFilter = $request->input('class');

		        list($class_id, $section_id) = explode('-', $classFilter, 2);
		        if (!empty($class_id)) {
		            $query->where('s.class_id', $class_id);
		        }
		        if (!empty($section_id)) {
		            $query->where('s.section_id', $section_id);
		        }
		    }


		    $status = $request->input('status');
			$tests  = $request->input('test', []);

			$testColumnMap = [
				'twenty_m_pacer'      => '20m_pacer',
				'fifteen_m_pacer'     => '15m_pacer',
				'oneMile_run_walk'    => '1mile_run_walk',
				'shoulder_stretch'    => 'shoulder_stretch',
				'sit_and_reach'       => 'sit_and_reach',
				'modified_apley_test' => 'modified_apley_test',
				'curlup'              => 'curlup',
				'modified_curlup'     => 'modified_curlup',
				'dumbbell_press'      => 'dumbbell_press',
				'pullup'              => 'pullup',
				'pushup'              => 'pushup',
				'seated_pushup'       => 'seated_pushup',
				'trunk_lift'          => 'trunk_lift',
				'Isometric_pushup'    => 'Isometric_pushup',
				'reverse_curl'        => 'reverse_curl',
				'modified_pullup'     => 'modified_pullup',
				'push_walk'           => '40m_push_walk',
				'cwsn_bmi'            => 'cwsn_bmi',
				'height'              => 'height',
				'weight'              => 'weight',
			];

			// if (in_array($status, ['complete', 'incomplete'])) {

			// 	$dbTests = [];

			// 	foreach ($tests as $test) {
			// 		if (isset($testColumnMap[$test])) {
			// 			$dbTests[] = $testColumnMap[$test];
			// 		}
			// 	}
			// 	if (empty($dbTests)) {
			// 		$dbTests = array_values($testColumnMap);
			// 	}

			// 	if ($status === 'complete') {
			// 		$query->where(function ($q) use ($dbTests) {

			// 			foreach ($dbTests as $column) {
			// 				$q->whereNotNull("r.$column")
			// 				->where("r.$column", '<>', '');
			// 			}

			// 		});

			// 	} else {
			// 		$query->where(function ($q) use ($dbTests) {
			// 			foreach ($dbTests as $column) {
			// 				$q->orWhereNull("r.$column")
			// 				->orWhere("r.$column", '');
			// 			}

			// 		});
			// 	}
			// }

			if (in_array($status, ['complete', 'incomplete'])) {
				$dbTests = [];
				foreach ($tests as $test) {
					if (isset($testColumnMap[$test])) {
						$dbTests[] = $testColumnMap[$test];
					}
				}
				if (empty($dbTests)) {
					$dbTests = array_values($testColumnMap);
				}

				$dbTests = array_unique($dbTests);
				$requiredBmiTests = [
					'cwsn_bmi',
					'height',
					'weight'
				];

				$additionalTests = array_values(
					array_diff($dbTests, $requiredBmiTests)
				);

				if ($status === 'complete') {

					$query->where(function ($q) use ($additionalTests) {
						$q->whereNotNull('r.cwsn_bmi')
						->where('r.cwsn_bmi', '<>', '');

						$q->whereNotNull('r.height')
						->where('r.height', '<>', '');

						$q->whereNotNull('r.weight')
						->where('r.weight', '<>', '');

						if (count($additionalTests) >= 3) {
							$q->where(function ($subQuery) use ($additionalTests) {
								$validExpression = collect($additionalTests)
									->map(function ($column) {
										return "CASE
													WHEN r.`$column` IS NOT NULL
														AND r.`$column` <> ''
													THEN 1
													ELSE 0
												END";
									})
									->implode(' + ');

								$subQuery->whereRaw(
									"($validExpression) >= 3"
								);
							});
						} else {
							$q->whereRaw('1 = 0');
						}
					});

				} else {
					$query->where(function ($q) use ($additionalTests) {

						// BMI missing
						$q->whereNull('r.cwsn_bmi')
						->orWhere('r.cwsn_bmi', '');

						// Height missing
						$q->orWhereNull('r.height')
						->orWhere('r.height', '');

						// Weight missing
						$q->orWhereNull('r.weight')
						->orWhere('r.weight', '');

						if (count($additionalTests) >= 3) {

							$validExpression = collect($additionalTests)
								->map(function ($column) {
									return "CASE
												WHEN r.`$column` IS NOT NULL
													AND r.`$column` <> ''
												THEN 1
												ELSE 0
											END";
								})
								->implode(' + ');

							$q->orWhereRaw(
								"($validExpression) < 3"
							);

						} else {
							$q->orWhereRaw('1 = 1');
						}
					});
				}
			}

	        $searchValue = $request->input('search.value');
	        if (!empty($searchValue)) {
			    $query->where(function ($q) use ($searchValue) {
			        $q->orWhere('s.student_name', 'LIKE', "%{$searchValue}%")
			          ->orWhere('s.rollno', 'LIKE', "%{$searchValue}%");
			    });
			}


	        if ($request->has('order')) {
	            $order = $request->order[0];
	            $colIndex = $order['column'];
	            $direction = $order['dir']; // asc | desc
	            $colName = $request->columns[$colIndex]['data'];

	            $sortableColumns = [
	                'class_name'     => DB::raw("display_classname"),
					'rollno'   		 => 's.rollno',
	                'student_name'   => 's.student_name',
					'age'   		 => 's.dob',
	                '20m_pacer'          => 'r.20m_pacer',
					'15m_pacer'          => 'r.15m_pacer',
					'1mile_run_walk'     => 'r.1mile_run_walk',
					'shoulder_stretch'   => 'r.shoulder_stretch',
					'sit_and_reach'      => 'r.sit_and_reach',
					'modified_apley_test'=> 'r.modified_apley_test',
					'curlup'             => 'r.curlup',
					'modified_curlup'    => 'r.modified_curlup',
					'dumbbell_press'     => 'r.dumbbell_press',
					'pullup'             => 'r.pullup',
					'pushup'             => 'r.pushup',
					'seated_pushup'      => 'r.seated_pushup',
					'trunk_lift'         => 'r.trunk_lift',
					'Isometric_pushup'   => 'r.Isometric_pushup',
					'reverse_curl'       => 'r.reverse_curl',
					'modified_pullup'    => 'r.modified_pullup',
					'40m_push_walk'      => 'r.40m_push_walk',
					'cwsn_bmi'           => 'r.cwsn_bmi',
					'height'             => 'r.height',
					'weight'             => 'r.weight',

	            ];

	            if (array_key_exists($colName, $sortableColumns)) {
	                $query->orderBy($sortableColumns[$colName], $direction);
	            }
	        }

            $recordsTotal = $query->count();
        	$draw = intval($request->input('draw'));
        	$studentlist = $query->skip($start)
            ->take($length == -1 ? $recordsTotal : $length)->get();
	
	        $flattenedList = $studentlist->map(function ($item) use ($role_id, $TermMasterId) {
				$id  = Crypt::encryptString($item->id);
           		$view_link = route('cwsn.reports.view', ['id' => $id, 'term_id' => $TermMasterId]);

	        	// $view_link = route('reports.view', ['id' => Crypt::encryptString($item->id)]);

				$age = \Carbon\Carbon::parse($item->dob)->age;
				$isValid = $this->isValidAge($item->class_id, $age);

				$studentAge = $isValid ? $age : $age;

				$oneMile_time_taken = is_numeric($item->TimeTaken) ? $item->TimeTaken	: '---';
				$one_mile_score = is_numeric($item->oneMile_run_walk) ? $item->oneMile_run_walk	: '---';
				if(in_array((int) $one_mile_score, [1000, 1001, 1002])){
					$one_mile_score = 'Not Calculated';
				}

	            return [
	                'id' => $item->id,
	                'student_name' => $item->student_name,					
					'age'			=> $studentAge,
					'invalid_age' => $isValid ? 0 : 1, 
					'pwd_type_id' => $item->pwd_type_id,
	           		'class_name' => $item->display_classname.'-'. $item->section,
	           		'rollno' => $item->rollno ?? 'N.A.',
					'twenty_m_pacer' => isset($item->twenty_m_pacer) ? (int)$item->twenty_m_pacer : '---',
					'fifteen_m_pacer' => isset($item->fifteen_m_pacer) ? (int)$item->fifteen_m_pacer : '---',
					'oneMile_run_walk' => $one_mile_score,
					'oneMile_time_taken' => $oneMile_time_taken,
					'curlup' => isset($item->curlup) ? (int)$item->curlup : '---',
	                'modified_curlup' => isset($item->modified_curlup) ? (int)$item->modified_curlup : '---',
	                'pushup' => isset($item->pushup) ? (int)$item->pushup : '---',
	                'modified_pullup' => isset($item->modified_pullup) ? (int)$item->modified_pullup : '---',
	                'pullup' => isset($item->pullup) ? (int)$item->pullup : '---',
	                'trunk_lift' => isset($item->trunk_lift) ? (int)$item->trunk_lift / 10 : '---',					
	                'Isometric_pushup' => is_numeric($item->Isometric_pushup)
											? sprintf(
												'%02d:%02d.%02d',
												floor((int)$item->Isometric_pushup / 60000),
												floor(((int)$item->Isometric_pushup % 60000) / 1000),
												floor(((int)$item->Isometric_pushup % 1000) / 10)
											)
											: '---',
	                'seated_pushup' => is_numeric($item->seated_pushup)
											? sprintf(
												'%02d:%02d.%02d',
												floor((int)$item->seated_pushup / 60000),
												floor(((int)$item->seated_pushup % 60000) / 1000),
												floor(((int)$item->seated_pushup % 1000) / 10)
											)
											: '---',
	                'push_walk' => isset($item->push_walk) ? ((int)$item->push_walk === 1 ? 'Pass' : 'Fail') : '---',
	                'reverse_curl' => isset($item->reverse_curl) ? ((int)$item->reverse_curl === 1 ? 'Pass' : 'Fail') : '---',
	                'dumbbell_press' => isset($item->dumbbell_press) ? (int)$item->dumbbell_press : '---',
	                'modified_apley_test' => isset($item->modified_apley_test) ? $item->modified_apley_test : '---',
					'sit_and_reach' => isset($item->sit_and_reach) ? $item->sit_and_reach : '---',
					'shoulder_stretch' => isset($item->shoulder_stretch) ? $item->shoulder_stretch : '---',
	                'cwsn_bmi' => isset($item->cwsn_bmi) ? round((float)$item->cwsn_bmi, 2) : '---',
	                'height' => $item->height ?? '---',
	                'weight' => $item->weight ?? '---',	                
	                'view_link' => $view_link,
	            ];
	        });

            return response()->json([
	            'draw' => intval($draw),
	            'recordsTotal' => $recordsTotal,
	            'recordsFiltered' => $recordsTotal,
	            'data' => $flattenedList
	        ]);
        }


       	$title = 'CWSN Test Status (Class 5 to 12)';
        return view('reports.summary.cwsn-summary', compact('title','classList','ajaxUrl','terms','TermMasterId'));
    }

	public function ViewCWSNFitnessReport($id=null, $term_id=null)	 {

		try{

			if($id){
				$studentId = Crypt::decryptString($id);
			}else{
				$studentId = Auth::guard('sstudent')->user()->id;
			}

			$studentsData = $this->getStudentData($studentId);
			$pwd_cat_id = $studentsData->pwd_cat_id;
			
			if (!empty($term_id)) {
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $term_id);
				} else {
					$selectedTermId = $this->getTermId($studentsData->schools_id);
					
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $selectedTermId);
			}
		
			$currentTermId  = $termIds[0] ?? null;
			$previousTermId = $termIds[1] ?? null;
			
			
			$dob          = Carbon::parse($studentsData->dob);
			$studentAge   = $dob->age;
			$studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
			$ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));
			
			// Fetch report + benchmarks
			$reportData = $this->getReportData($studentId, $termIds);
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
			$academicYear = DB::table('term_masters')->where('id', $currentTermId)->value('academic_year');
			// echo"<pre>";print_r($mappedReport);exit();
			return view('reports.fitness.html.cwsn-report', compact('studentsData','orderedReportData','getBmiBenchmark','academicYear'));
		
		} catch (\Exception $e) {
	      
	        Log::error("Error Viewing fitness report: " . $e->getMessage(), [
	            'student_id' => $id ?? 'Auth User',
	            'file' => $e->getFile(),
	            'line' => $e->getLine(),
	            'trace' => $e->getTraceAsString()
	        ]);

	        return redirect()->back()->with('error', 'An error occurred while Viewing the report. Please try again later.');
	    }
	}

	public function downloadCWSNFitnessReport($id = null, $term_id= null){
				
		try {			
			if($id){
				$studentId = Crypt::decryptString($id);
			}else{
				$studentId = Auth::guard('sstudent')->user()->id;
			}

			$studentsData = $this->getStudentData($studentId);
			$pwd_cat_id = $studentsData->pwd_cat_id;
			
			if (!empty($term_id)) {
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $term_id);
				} else {
					$selectedTermId = $this->getTermId($studentsData->schools_id);
					
				$termIds = $this->getCurrentAndPreviousTermIds($studentsData->schools_id, (int) $selectedTermId);
			}
		
			$currentTermId  = $termIds[0] ?? null;
			$previousTermId = $termIds[1] ?? null;
			
			
			$dob          = Carbon::parse($studentsData->dob);
			$studentAge   = $dob->age;
			$studentGender = strtolower($studentsData->gender) === 'male' ? 'Boys' : 'Girls';
			$ageGender    = $studentAge . strtolower(substr($studentsData->gender, 0, 1));
			
			// Fetch report + benchmarks
			$reportData = $this->getReportData($studentId, $termIds);
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
			$academicYear = DB::table('term_masters')->where('id', $currentTermId)->value('academic_year');
			// echo"<pre>";print_r($orderedReportData);exit();
			$pdf = Pdf::loadView('reports.fitness.pdf.cwsn-pdf-report', compact('studentsData','orderedReportData', 'getBmiBenchmark','academicYear'));

			return $pdf->stream('fitness_report.pdf');

		} catch (\Exception $e) {
	      
	        Log::error("Error generating fitness report: " . $e->getMessage(), [
	            'student_id' => $id ?? 'Auth User',
	            'file' => $e->getFile(),
	            'line' => $e->getLine(),
	            'trace' => $e->getTraceAsString()
	        ]);

	        return redirect()->back()->with('error', 'An error occurred while generating the report. Please try again later.');
	    }
    }

}
