<?php

namespace App\Http\Controllers\Icse;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\AuditHelper;
use App\Models\Region;
use App\Models\Sstudent;
use App\Models\SchoolUsers;
use App\Models\CICSESchool;
use App\Models\DeletedStudent;
use App\Models\School;
use App\Models\CustomClass;
use App\Models\EmailReportBatch;
use App\Models\EmailReportRequest;
use App\Traits\ReportHelperTrait;
use App\Exports\InvalidEmailsExport;
use App\Exports\AdminCredentials;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\GenerateSchoolReportsMasterJob;
use App\Models\AuditLogs;

class CicseController extends Controller
{
    use ReportHelperTrait;

    public function __construct() {
        $this->middleware(['auth', 'role:9,2']);
        $this->higherClasses = [4, 5, 6, 7, 8, 9, 10, 11, 12];
        $this->cwsnClasses = [5, 6, 7, 8, 9, 10, 11, 12];
        $this->lowerClass = [1, 2, 3];
    }

    public function index(Request $request){

        $title = 'CISCE Dashboard';
        $excludedSchools = ['AK001', 'AK002', 'AK003'];

        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
        [$startYear, $endYear] = explode('-', $academicYear);
        $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

        $selectedYear = $request->academic_year ?? $academicYear;
        $authUser = Auth::user();
        $role_id = $authUser->role_id;

        $designation = null;
        $userRegion = null;
        $userZone = null;

        if ($role_id != 9) {
            $userMeta = DB::table('users')
                ->join('usermetas', 'usermetas.user_id', '=', 'users.id')
                ->leftJoin('regions', 'regions.id', '=', 'usermetas.region_id')
                ->leftJoin('zones', 'zones.id', '=', 'usermetas.zone_id')
                ->where('users.id', Auth::user()->id)
                ->select(
                    'users.id',
                    'users.role_id',
                    'usermetas.designation',
                    'usermetas.region_id',
                    'usermetas.zone_id',
                    'regions.name as region_name',
                    'zones.name as zone_name'
                )
                ->first();
            $designation = $userMeta->designation ?? null;

            $userRegionId = $userMeta->region_id ?? null;
            $userZoneId   = $userMeta->zone_id ?? null;

            $userRegion = $userMeta->region_name ?? null;
            $userZone   = $userMeta->zone_name ?? null;
        }
        
        $isRegionalHead = ($role_id == 2 && $designation == 5);
        $isZonalHead    = ($role_id == 2 && $designation == 6);

        $traines = DB::table('users')
        ->join('school_trainers', 'school_trainers.trainer_id', '=', 'users.id')
        ->join('schools', 'schools.id', '=', 'school_trainers.school_id')
        ->select(
            'users.id',
            'schools.region',
            'schools.zonename',
            'schools.state',
            'schools.district'
        )
        ->where('users.role_id', 3);


        $baseQuery = DB::table('schools_assessment as s')
            ->where('academic_year', $selectedYear)
            ->whereNotIn('s.school_code', $excludedSchools);
        
        $fitnessQuery = DB::table('flattened_fitness_data')
            ->select(
                'skill_name',
                'level',
                DB::raw('SUM(total_count) as total_students')
            ); 

        if ($isRegionalHead) {

            $fitnessQuery->whereRaw(
                'LOWER(region) = ?',
                [strtolower(trim($userRegion))]
            );
            $baseQuery->whereRaw(
                'LOWER(region) = ?',
                [strtolower(trim($userRegion))]
            );
            $traines->whereRaw(
                'LOWER(schools.region) = ?',
                [strtolower(trim($userRegion))]
            );

        } elseif ($isZonalHead) {

            $fitnessQuery->whereRaw(
                'LOWER(zonename) = ?',
                [strtolower(trim($userZone))]
            );
            $baseQuery->whereRaw(
                'LOWER(zonename) = ?',
                [strtolower(trim($userZone))]
            );
            $traines->whereRaw(
                'LOWER(schools.zonename) = ?',
                [strtolower(trim($userZone))]
            );

            // Additional region restriction
            if (!empty($userRegion)) {
                $fitnessQuery->whereRaw(
                    'LOWER(region) = ?',
                    [strtolower(trim($userRegion))]
                );
                $baseQuery->whereRaw(
                    'LOWER(region) = ?',
                    [strtolower(trim($userRegion))]
                );
                $traines->whereRaw(
                    'LOWER(schools.region) = ?',
                    [strtolower(trim($userRegion))]
                );
            }
        }

        if (!$isRegionalHead && !$isZonalHead) {
            if ($request->filled('region')) {
                list($regionName, $regionId) = explode('|', $request->region);
                $fitnessQuery->where('region', $regionName);
                $baseQuery->where('region', $regionName);
                $traines->where('schools.region', $regionName);
            }

            if ($request->filled('zone')) {
                $fitnessQuery->where('zonename', $request->zone);
                $baseQuery->where('zonename', $request->zone);
                $traines->where('schools.zonename', $request->zone);
            }
        }

        // if ($request->filled('region')) {
        //     list($regionName, $regionId) = explode('|', $request->region);
        //     $fitnessQuery->where('region', $regionName);
        //     $baseQuery->where('region', $regionName);
        //     $traines->where('schools.region', $regionName);
        // }

        // if ($request->filled('zone')) {
        //     $fitnessQuery->where('zonename', $request->zone);
        //     $baseQuery->where('zonename', $request->zone);
        //     $traines->where('schools.zonename', $request->zone);
        // }

        if ($request->filled('state')) {
            list($stateId, $stateName) = explode('|', $request->state);
            $fitnessQuery->where('state', $stateName);
            $baseQuery->where('state', $stateName);
            $traines->where('schools.state', $stateName);
        }

        if ($request->filled('district')) {
            list($districtId, $districtName) = explode('|', $request->district);
            $fitnessQuery->where('district', $districtName);
            $baseQuery->where('district', $districtName);
            $traines->where('schools.district', $districtName);
        }
        
        $regSchools = $baseQuery->count('s.school_code');
        $regTrainers = $traines->count('users.id');

        $completedSchools = (clone $baseQuery)
            ->whereColumn('s.registered_students', 's.completed')
            ->where('s.registered_students', '>', 0)
            ->count();

        $ongoingSchools = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('ongoing', '>', 0)
                ->orWhere(function ($q2) {
                    $q2->where('ongoing', 0)
                        ->whereColumn('registered_students', '!=', 'completed')
                        ->whereColumn('registered_students', '!=', 'yet_to_start');
                });
            })
            ->count();

        $yetToStartSchools = (clone $baseQuery)
            ->whereColumn('s.registered_students', 's.yet_to_start')
            ->where('s.registered_students', '>=', 0)
            ->count();

        $totals = (clone $baseQuery)
            ->selectRaw('
                COALESCE(SUM(s.registered_students),0) as total_students,
                COALESCE(SUM(s.completed),0) as total_completed,
                COALESCE(SUM(s.ongoing),0) as total_ongoing,
                COALESCE(SUM(s.yet_to_start),0) as total_yet_to_start
            ')
            ->first();

        $topSchools = (clone $baseQuery)
            ->get();

        $levelColors = [
            'L1'=>'#fe4a5d','L2'=>'#ffaa62','L3'=>'#ffd26e',
            'L4'=>'#74c4d6','L5'=>'#a3d55f','L6'=>'#6bc04b','L7'=>'#00953b',
            'UW'=>'#a3d55f', 'N'=>'#00953b', 'OW'=>'#ffaa62', 'OB'=>'#fe4a5d'
        ];
            
        

        $allRows = $fitnessQuery
            ->groupBy('skill_name', 'level')
            ->orderBy('skill_name')
            ->get();
        
        $levels = ['L1','L2','L3','L4','L5','L6','L7'];
        $bmiLevels = ['UW','N','OW','OB'];

        $skillCategories = $allRows
            ->whereIn('level', $levels)
            ->pluck('skill_name')->unique()->values()->toArray();

        $bmiCategories = $allRows
            ->whereIn('level', $bmiLevels)
            ->pluck('skill_name')->unique()->values()->toArray();

        $grouped = [];
        foreach ($allRows as $row) {
            $grouped[$row->level][$row->skill_name] = (int)$row->total_students;
        }
        $buildSeries = function($levelsList, $categories) use ($grouped, $levelColors) {
            $series = [];

            foreach ($levelsList as $level) {
                $data = [];

                foreach ($categories as $skill) {
                    $data[] = $grouped[$level][$skill] ?? 0;
                }

                $series[] = [
                    'name' => "Level - $level",
                    'data' => $data,
                    'color' => $levelColors[$level]
                ];
            }

            return $series;
        };

        $skillSeries = $buildSeries($levels, $skillCategories);
        $bmiSeries = $buildSeries($bmiLevels, $bmiCategories);

        if ($isRegionalHead) {
            $regions = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->orderBy('name')
                ->get();

            $zones = DB::table('zones')
                ->where('region_id', $userRegionId)
                ->get();

        } elseif ($isZonalHead) {

            $regions = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->get();

            $zones = DB::table('zones')
                ->where('id', $userZoneId)
                ->get();

        } else {

            $regions = Region::where('status', 1)->orderBy('name')->get();
            $zones = DB::table('zones')->orderBy('name')->get();
        }

        $states = DB::table('states')->orderBy('name')->get();
        $districts = DB::table('districts')->orderBy('name')->get();
        
        return view('icse.index', [
            'title' => $title,
            'academicYear' => $academicYear,
            'previousAcademicYear' => $previousAcademicYear,
            'selectedYear' => $selectedYear,
            'regSchools' => $regSchools,
            'regTrainers' => $regTrainers,
            'completedSchools' => $completedSchools,
            'ongoingSchools' => $ongoingSchools,
            'yetToStartSchools' => $yetToStartSchools,
            'totalStudents' => $totals->total_students,
            'totalCompleted' => $totals->total_completed,
            'totalOngoing' => $totals->total_ongoing,
            'totalYetToStart' => $totals->total_yet_to_start,
            'topSchools' => $topSchools,
            'skillCategories' => $skillCategories,
            'skillSeries'        => $skillSeries,
            'bmiCategories' => $bmiCategories,
            'bmiSeries'        => $bmiSeries,
            'selectedRegion'   => $request->region,
            'selectedZone'     => $request->zone,
            'selectedState'    => $request->state,
            'selectedDistrict' => $request->district,
            'regions' => $regions,
            'zones' => $zones,
            'states' => $states,
            'districts' => $districts,
        ]);
    }

    public function schoolList(Request $request)  {
        $title = 'School Summary';
        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
        [$startYear, $endYear] = explode('-', $academicYear);
        $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

        $selectedYear = $request->academic_year ?? $academicYear;
        
        $authUser = Auth::user();
        $role_id = $authUser->role_id;

        $designation = null;
        $userRegion = null;
        $userZone = null;

        if ($role_id != 9) {
            $userMeta = DB::table('users')
                ->join('usermetas', 'usermetas.user_id', '=', 'users.id')
                ->leftJoin('regions', 'regions.id', '=', 'usermetas.region_id')
                ->leftJoin('zones', 'zones.id', '=', 'usermetas.zone_id')
                ->where('users.id', Auth::user()->id)
                ->select(
                    'users.id',
                    'users.role_id',
                    'usermetas.designation',
                    'usermetas.region_id',
                    'usermetas.zone_id',
                    'regions.name as region_name',
                    'zones.name as zone_name'
                )
                ->first();
            $designation = $userMeta->designation ?? null;

            $userRegionId = $userMeta->region_id ?? null;
            $userZoneId   = $userMeta->zone_id ?? null;

            $userRegion = $userMeta->region_name ?? null;
            $userZone   = $userMeta->zone_name ?? null;
        }
        
        $isRegionalHead = ($role_id == 2 && $designation == 5);
        $isZonalHead    = ($role_id == 2 && $designation == 6);
        
        if ($request->ajax()) {
            $start = $request->input('start', 0);
            $length = $request->input('length', 100);

            $query = DB::table('schools as s')
                ->leftJoin('schools_assessment as sa', 'sa.school_id', '=', 's.id')   
                ->select([
                    's.id AS school_id',
                    's.school_code',
                    's.school_name',
                    's.school_principal',
                    's.state',
                    's.district',
                    's.region',
                    's.zonename',
                    's.address',
                    's.city',
                    'sa.academic_year',
                    'sa.registered_students AS students_count',
                ])
                ->where('sa.academic_year', $selectedYear)
                ->whereNotIn('s.id', [3129, 3130, 3131])
                ->orderBy('s.school_code');


            // $query = DB::table('schools as s')
            //     ->leftJoinSub(
            //         DB::table('students')
            //             ->select([
            //                 'school_id',
            //                 DB::raw('COUNT(*) AS students_count'),
            //                 DB::raw('? AS academic_year'),
            //             ])
            //             ->where('academic_year', $selectedYear)
            //             ->where('status', 'active')
            //             ->groupBy('school_id')
            //             ->addBinding($selectedYear, 'select'),
            //         'st',
            //         function ($join) {
            //             $join->on('st.school_id', '=', 's.id');
            //         }
            //     )
            //     ->select([
            //         's.id AS school_id',
            //         's.school_code',
            //         's.school_name',
            //         's.school_principal',
            //         's.state',
            //         's.district',
            //         's.region',
            //         's.zonename',
            //         's.address',
            //         's.city',
            //         DB::raw('COALESCE(st.students_count, 0) AS students_count'),
            //         'st.academic_year',
            //     ])
            //     ->whereNotIn('s.id', [3129, 3130, 3131])
            //     ->orderBy('s.school_code');


            // echo"<pre>";print_r($query->get());exit();

            $recordsTotal = clone $query;
            $totalCount = $recordsTotal->count();

            if ($isRegionalHead) {

                $query->whereRaw(
                    'LOWER(s.region) = ?',
                    [strtolower(trim($userRegion))]
                );

            } elseif ($isZonalHead) {

                $query->whereRaw(
                    'LOWER(s.zonename) = ?',
                    [strtolower(trim($userZone))]
                );

                // Additional region restriction
                if (!empty($userRegion)) {
                    $query->whereRaw(
                        'LOWER(s.region) = ?',
                        [strtolower(trim($userRegion))]
                    );
                }
            }

            if (!$isRegionalHead && !$isZonalHead) {
                if ($request->filled('region')) {
                    $regionData = explode('|', $request->region);
                    $regionName = $regionData[0];
                    $query->whereRaw(
                        'LOWER(s.region) = ?',
                        [strtolower(trim($regionName))]
                    );
                }
                if ($request->filled('zone')) {
                    $query->where('s.zonename', $request->zone);
                }
            }

            $uploadedStatus = $request->has('uploaded_status') ? $request->uploaded_status : 'uploaded';

            if ($uploadedStatus === 'uploaded') {
                $query->where('sa.registered_students', '>', 0);
            } elseif ($uploadedStatus === 'not_uploaded') {
                $query->where(function ($q) {
                    $q->whereNull('sa.registered_students')
                    ->orWhere('sa.registered_students', '=', 0);
                });
            }

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('s.school_code', 'like', "%$search%")
                      ->orWhere('s.school_name', 'like', "%$search%")
                      ->orWhere('s.district', 'like', "%$search%");
                });
            }
            $recordsFiltered = clone $query;
            $filteredCount = $recordsFiltered->count();

            $orderColIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'asc');
            $orderColumn = $request->input("columns.$orderColIndex.data");

            $sortableColumns = [
                'school_code' => 's.school_code',
                'school_name' => 's.school_name',
                'region' => 's.region',
                'zonename' => 's.zonename',
                'district' => 's.district',
                'state' => 's.state'
            ];

            if (isset($sortableColumns[$orderColumn])) {
                $query->orderBy($sortableColumns[$orderColumn], $orderDir);
            }

            if ((int) $length === -1) {
                $schoollist = $query->get();
            } else {
                $schoollist = $query->skip($start)->take($length)->get();
            }

            $flattenedList = $schoollist->map(function ($item) {
                return [
                    'id' => $item->school_id,
                    // 'user_id' => $item->user_id,
                    'school_code' => $item->school_code,
                    'school_name' => $item->school_name,
                    'school_principal' => $item->school_principal,
                    'state' => $item->state,
                    'district' => $item->district,
                    'region' => $item->region,
                    'zonename' => $item->zonename,
                    'address' => $item->address,
                    'city' => $item->city,
                    'student_count' => $item->students_count ?? 0,
                    'paid_count' => $item->paid_count ?? 0,
                    'unpaid_count' => $item->unpaid_count ?? 0,
                    'view_link' => route('cicse.school.view', ['id' => $item->school_id]),
                ];
            });

            // 8. Return Proper DataTables Object
            return response()->json([
                'status' => 'success',
                'data' => $flattenedList,
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $filteredCount 
            ]);
        }

        if ($isRegionalHead) {
            $region = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->orderBy('name')
                ->get();

            $zones = DB::table('zones')
                ->where('region_id', $userRegionId)
                ->get();

        } elseif ($isZonalHead) {

            $region = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->get();

            $zones = DB::table('zones')
                ->where('id', $userZoneId)
                ->get();

        } else {

            $region = Region::where('status', 1)->orderBy('name')->get();
            $zones = DB::table('zones')->orderBy('name')->get();
        }

        

        return view('icse.schoollist', compact('title', 'region', 'zones', 'academicYear', 'previousAcademicYear', 'selectedYear'));
    }
    

    public function schoolDetail($id, Request $request) {

        $school = CICSESchool::where('id', $id)->firstOrFail();

        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
        [$startYear, $endYear] = explode('-', $academicYear);
        $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

        $selectedYear = $request->academic_year ?? $academicYear;

        if ($request->ajax()) {

            $students = DB::table('schools')
                ->join('students', 'students.school_id', '=', 'schools.id')
                ->leftJoin('class', 'students.class_id', '=', 'class.id')
                ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
                ->select(
                    'schools.id as schools_id',
                    'schools.school_code as school_code',
                    'students.id as student_id',
                    'students.student_uid',
                    'students.student_name',
                    'students.gender',
                    'students.apaarId',
                    'students.dob',
                    'students.email_id',
                    'students.rollno',
                    'students.status',
                    DB::raw("CASE 
                        WHEN custom_classes.nomenclature IS NOT NULL 
                             AND custom_classes.nomenclature <> '' 
                        THEN custom_classes.nomenclature 
                        ELSE class.name 
                    END AS display_classname"),
                    'custom_classes.section'
                )
                //->where('schools.id', $id);
                ->where('students.school_code', $school->school_code)
                ->where('students.academic_year', $selectedYear);

            return datatables()->of($students)

                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = strtolower($request->input('search.value'));

                        $query->where(function ($q) use ($search) {
                            $q->whereRaw('LOWER(students.student_uid) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(students.student_name) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(students.rollno) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(students.email_id) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(custom_classes.nomenclature) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('LOWER(class.name) LIKE ?', ["%{$search}%"]);
                        });
                    }
                })

                ->addIndexColumn()

                ->editColumn('gender', function ($row) {
                    return ucfirst($row->gender ?? '');
                })

                ->make(true);
        }
        $studentCount = DB::table('students')
        ->where('school_id', (string) $school->id)
        ->where('academic_year', $selectedYear)
        ->where('status', 'active')
        ->selectRaw('
            COUNT(*) as total_students,
            SUM(CASE WHEN is_pwd = "1" THEN 1 ELSE 0 END) as cwsn_students
        ')
        ->first();

        $title = 'School Details';
        return view('icse.view_school', compact('school','title','id', 'academicYear', 'previousAcademicYear', 'selectedYear', 'studentCount'));
    }


    public function getDistricts($state_id)
    {
        $districts = \DB::table('districts')
                        ->where('state_id', $state_id)
                        ->pluck('name', 'id');

        return response()->json($districts);
    }

    public function addSchoolView(Request $request){
        $title = 'School Registration';
        $states = DB::table('states')->get();
        $district = DB::table('districts')->get();
        $regions = DB::table('regions')->get();

        $newReg = DB::table('schools')->whereIn('status', [0, 2])->get();
        

        if ($request->ajax()) {
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 100);

            $baseQuery = DB::table('schools as s')
                ->leftjoin('school_reference', 'school_reference.school_id', '=', 's.id')
                ->leftjoin('users as u', 'u.id', '=', 'school_reference.school_user_id')
                ->leftjoin('usermetas', 'usermetas.user_id', '=', 'u.id')
                ->select(
                    's.id as school_id',
                    's.school_code as school_code',
                    's.school_name as school_name',
                    's.school_email as school_email',
                    's.region as region',
                    's.zonename as zonename',
                    's.state',
                    's.state_id',
                    's.district',
                    's.district_id',
                    's.shift as school_shift',
                    'u.name as principal_name',
                    'u.email',
                    'u.position',
                    's.pincode',
                    's.city',
                    's.address',
                    's.status',
                    'usermetas.gender',
                )
                ->whereNotIn('s.school_code', ['AK001', 'AK002', 'AK003'])
                ->whereIn('s.status', [0, 2]);              

            $query = clone $baseQuery;

            if ($request->filled('status')) {
                $query->where('s.status', $request->status);
            }

            if($request->has('schoolId')){
                $query->where('s.id', $request->schoolId);
            }
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('s.school_code', 'like', "%{$search}%")
                    ->orWhere('s.school_name', 'like', "%{$search}%");
                });
            }
            
            $orderIndex = $request->input('order.0.column');
            $orderDir   = $request->input('order.0.dir', 'asc');
            $orderCol   = $request->input("columns.$orderIndex.data");

            $sortable = [
                'school_code' => 's.school_code',
                'school_name' => 's.school_name',
            ];

            if (isset($sortable[$orderCol])) {
                $query->orderBy($sortable[$orderCol], $orderDir);
            }

            $recordsTotal    = $baseQuery->count();
            $recordsFiltered = $query->count();

            if ($length !== -1) {
                $query->skip($start)->take($length);
            }

            return response()->json([
                'draw'            => (int) $request->draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $query->get(),
            ]);
        }


        return view('icse.addschool', compact('title','states','district','regions', 'newReg'));
    }

    public function addSchoolRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_code'      => 'required|unique:schools,school_code',
            'school_name'      => 'required|string|max:255',
            'school_email'     => 'required|email|unique:schools,school_email',
            'shift'            => 'required',
            'principal_name'   => 'required|string|max:255',
            'principal_email'  => 'required|email|unique:users,email',
            'position'          => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors()
            ]);
        }
        
        list($regionName, $regionId) = explode('|', $request->region);
        list($stateId, $stateName) = explode('|', $request->state);
        list($districtId, $districtName) = explode('|', $request->district); 
        
        $schoolCode = strtoupper($request->school_code);
        $default_email = 'Head.' . $schoolCode . '@cisceschools.org';
        DB::beginTransaction();
        try {
            // 1. Insert data into School
            $schoolId = DB::table('schools')->insertGetId([
                'school_name'   => $request->school_name,
                'school_code'   => $schoolCode,
                'school_principal' => $request->principal_name,
                'school_email'  => $request->school_email,
                'city'          => $request->city,
                'state'         => $stateName,
                'state_id'      => $stateId,
                'district'      => $districtName,
                'district_id'   => $districtId,
                'region'        => $regionName,
                'shift'         => $request->shift,
                'zonename'     => $request->zone,
                'address'       => $request->address,
                'pincode'       => $request->pincode,
                'status'        => '1',
                'board'         => 'cisce',
                'added_date'    => now(),
            ]);
            
            // 2. Create Principal User
            $userId = DB::table('users')->insertGetId([
                'name'       => $request->principal_name,
                'email'      => $default_email,
                'userid'     => $request->principal_email,
                'password'   => Hash::make($schoolCode . '@f365'),
                'position'   => $request->position,
                'role_id'    => '4',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Insert into school_users
            DB::table('school_users')->insert([
                'school_id'  => $schoolId,
                'user_id'    => Auth::user()->id,
            ]);
            
            $today = new \DateTime();
            $year = (int)$today->format('Y');

            if ($today < new \DateTime("$year-04-01")) {
                $startYear = $year - 1;
                $endYear   = $year;
            } else {
                $startYear = $year;
                $endYear   = $year + 1;
            }

            $academicYear = $startYear . '-' . $endYear;

            $termStartDate = Carbon::create($startYear, 4, 1)->startOfDay();
            $termEndDate   = Carbon::create($endYear, 3, 31)->endOfDay();

            // 3. Insert into Terms
            DB::table('term_masters')->insert([
                'school_id'  => $schoolId,
                'term_name'  => 'Term-1',
                'camp_type'  => '1',
                'academic_year'  => $academicYear,
                'academic_year_start'    => $termStartDate,
                'academic_year_end'    => $termEndDate,
                'term_start_date'    => $termStartDate,
                'term_end_date'    => $termEndDate,
                'is_active'     => '1',
                'is_deleted'    => '0',
            ]);

            // 4. Insert into school_reference
            DB::table('school_reference')->insert([
                'school_user_id' => $userId,
                'school_id'      => $schoolId
            ]);

            // 5 isert into usermetas

            DB::table('usermetas')->insert([
                'user_id' => $userId,
                'state' => $stateName,
                'district' => $districtName,
                'state_id' => $stateId,
                'district_id' => $districtId,
                'region_id' =>$regionId,
                'school_id'  => $schoolId,
                'school_name'   => $request->school_name,
                'city' => $request->city,
                'gender' => $request->gender,
                'created_by' => Auth::user()->id,
            ]);


            DB::commit();

            return response()->json([
                'status'  => 1,
                'message' => 'School and Principal added successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function approveSchool(Request $request){
        try {

            $rules = [
                'school_id' => 'required|exists:schools,id',
            ];

            if ($request->action_type === 'reject') {
                $rules['reject_reason'] = 'required|string';
            } else {
                $rules += [
                    'school_code'      => 'required',
                    'school_name'      => 'required',
                    'school_email'     => 'required|email',
                    'school_shift'     => 'required',
                    'principal_name'   => 'required',
                    'principal_email'  => 'required|email',
                    'position'         => 'required',
                    'gender'           => 'required',
                    'pincode'          => 'nullable|digits:6',
                    'city'             => 'nullable|string',
                    'address'          => 'nullable|string',
                    'region'           => 'required|string',
                    'state'            => 'required|string',
                    'district'         => 'required|string',
                    'zonename'         => 'required|string',
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ]);
            }

            $stateParts = explode('|', $request->state);
            $districtParts = explode('|', $request->district);

            $stateName = $stateParts[1] ?? $request->state;
            $districtName = $districtParts[1] ?? $request->district;

            $schoolUserId = DB::table('school_reference')
                ->where('school_id', $request->school_id)
                ->value('school_user_id');

            $status = $request->action_type === 'reject' ? 2 : 1;
            if ($request->action_type === 'reject') {
                DB::table('schools')
                    ->where('id', $request->school_id)
                    ->update([
                        'status' => $status,                        
                    ]);
                DB::table('users')
                    ->where('id', $schoolUserId)
                    ->update([
                        'status' => $status,
                        'additional_info' => $request->reject_reason
                    ]);

                return response()->json([
                    'status' => 1,
                    'message' => 'School rejected successfully'
                ]);
            }

            DB::table('schools')
                ->where('id', $request->school_id)
                ->update([
                    'school_code'      => $request->school_code,
                    'school_name'      => $request->school_name,
                    'school_principal' => $request->principal_name,
                    'school_email'     => $request->school_email,
                    'city'             => $request->city,
                    'state'            => $stateName,
                    'district'         => $districtName,
                    'region'           => $request->region,
                    'shift'            => $request->school_shift,
                    'zonename'         => $request->zonename,
                    'address'          => $request->address,
                    'pincode'          => $request->pincode,
                    'status'           => $status,
                    'board'            => 'cisce',
                ]);

            if ($schoolUserId) {
                DB::table('users')
                    ->where('id', $schoolUserId)
                    ->update([
                        'name'   => $request->principal_name,
                        'email'  => $request->principal_email,
                        'status' => $status,
                    ]);
            }

            // Insert into Terms
            
            $currentYear = now()->year;
            $startDate = Carbon::createFromDate($currentYear, 4, 1)->startOfDay();
            $endDate = $startDate->copy()->addYear()->subDay()->endOfDay();

            $academicYear = $startDate->format('Y') . '-' . $endDate->format('Y');

            DB::table('term_masters')->insert([
                'school_id' => $request->school_id,
                'term_name' => 'Full-Term',
                'academic_year' => $academicYear,
                'academic_year_start' => $startDate,
                'academic_year_end' => $endDate,
                'term_start_date' => $startDate,
                'term_end_date' => $endDate,
                'camp_type' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'School approved successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
            //  return response()->json([
            //     'status' => 0,
            //     'message' => $e->getMessage(),
            //     'file' => $e->getFile(),
            //     'line' => $e->getLine(),
            // ], 500);
        }
    }

    // School-wise assessment status 

    public function schoolAssessmentStatus(Request $request){
        $title = "Payment & Assessments Status";

        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
        [$startYear, $endYear] = explode('-', $academicYear);
        $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

        $selectedYear = $request->academic_year ?? $academicYear;

        $authUser = Auth::user();
        $role_id = $authUser->role_id;

        $designation = null;
        $userRegion = null;
        $userZone = null;

        if ($role_id != 9) {
            $userMeta = DB::table('users')
                ->join('usermetas', 'usermetas.user_id', '=', 'users.id')
                ->leftJoin('regions', 'regions.id', '=', 'usermetas.region_id')
                ->leftJoin('zones', 'zones.id', '=', 'usermetas.zone_id')
                ->where('users.id', Auth::user()->id)
                ->select(
                    'users.id',
                    'users.role_id',
                    'usermetas.designation',
                    'usermetas.region_id',
                    'usermetas.zone_id',
                    'regions.name as region_name',
                    'zones.name as zone_name'
                )
                ->first();
            $designation = $userMeta->designation ?? null;

            $userRegionId = $userMeta->region_id ?? null;
            $userZoneId   = $userMeta->zone_id ?? null;

            $userRegion = $userMeta->region_name ?? null;
            $userZone   = $userMeta->zone_name ?? null;
        }
        
        $isRegionalHead = ($role_id == 2 && $designation == 5);
        $isZonalHead    = ($role_id == 2 && $designation == 6);
        
        if ($request->ajax()) {

            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 100);

            $baseQuery = DB::table('schools_assessment as s')->where('academic_year', $selectedYear)->where('s.registered_students','!=', 0)->whereNotIn('s.school_code', ['AK001', 'AK002', 'AK003']);

            $query = clone $baseQuery;
            $query->select(
                's.school_id',
                's.school_code',
                's.school_name',
                's.region',
                's.zonename',
                's.registered_students',
                's.completed',
                's.ongoing',
                's.yet_to_start',
                's.cwsn_count',
                's.pymt_count',
                's.email_sent',
                DB::raw('(s.registered_students - s.pymt_count) AS unpaid_students')
            );
            
            // if ($request->filled('region')) {
            //     $regionData = explode('|', $request->region);
            //     $regionName = $regionData[0];
            //     $regionId   = $regionData[1] ?? null;
            //     $query->whereRaw('LOWER(s.region) = ?', [strtolower($regionName)]);
            // }

            // if($request->filled('zone')){
            //     $query->where('s.zonename', $request->zone);
            // }

            if ($isRegionalHead) {

                $query->whereRaw(
                    'LOWER(s.region) = ?',
                    [strtolower(trim($userRegion))]
                );

            } elseif ($isZonalHead) {

                $query->whereRaw(
                    'LOWER(s.zonename) = ?',
                    [strtolower(trim($userZone))]
                );

                // Additional region restriction
                if (!empty($userRegion)) {
                    $query->whereRaw(
                        'LOWER(s.region) = ?',
                        [strtolower(trim($userRegion))]
                    );
                }
            }

            if (!$isRegionalHead && !$isZonalHead) {
                if ($request->filled('region')) {
                    $regionData = explode('|', $request->region);
                    $regionName = $regionData[0];
                    $query->whereRaw(
                        'LOWER(s.region) = ?',
                        [strtolower(trim($regionName))]
                    );
                }
                if ($request->filled('zone')) {
                    $query->where('s.zonename', $request->zone);
                }
            }

            if ($request->filled('status')) {

                if ($request->status === 'completed') {
                    $query->whereColumn('s.completed', 's.registered_students')
                    ->where('s.registered_students', '>', 0);
                } elseif ($request->status === 'ongoing') {
                    $query->where('s.ongoing', '>', 0);
                } elseif ($request->status === 'yet_to_start') {
                    $query->whereColumn('s.yet_to_start', 's.registered_students');
                }
            }

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('s.school_code', 'like', "%{$search}%")
                    ->orWhere('s.school_name', 'like', "%{$search}%")
                    ->orWhere('s.region', 'like', "%{$search}%");
                });
            }

            $orderIndex = $request->input('order.0.column');
            $orderDir   = $request->input('order.0.dir', 'asc');
            $orderCol   = $request->input("columns.$orderIndex.data");

            $sortable = [
                'school_code' => 's.school_code',
                'school_name' => 's.school_name',
                'region'      => 's.region',
                'zonename' => 's.zonename',

            ];

            
    


            if (isset($sortable[$orderCol])) {
                $query->orderBy($sortable[$orderCol], $orderDir);
            }
            $recordsTotal    = $baseQuery->count();
            $recordsFiltered = (clone $query)->count();

            if ($length !== -1) {
                $query->skip($start)->take($length);
            }

            return response()->json([
                'draw'            => (int) $request->draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $query->get(),
            ]);
        }

        if ($isRegionalHead) {
            $region = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->orderBy('name')
                ->get();

            $zones = DB::table('zones')
                ->where('region_id', $userRegionId)
                ->get();

        } elseif ($isZonalHead) {

            $region = Region::where('status', 1)
                ->where('id', $userRegionId)
                ->get();

            $zones = DB::table('zones')
                ->where('id', $userZoneId)
                ->get();

        } else {

            $region = Region::where('status', 1)->orderBy('name')->get();
            $zones = DB::table('zones')->orderBy('name')->get();
        }

        return view('icse.assessements_status', compact('title', 'region','zones', 'academicYear','previousAcademicYear','selectedYear'));
        
    }

    
    // Add/View Super admins

    public function viewSuperAdmin(Request $request){
        if (Auth::user()->role_id != 9) {
            return redirect()
                ->route('cicse.dashboard')
                ->with('error', 'You do not have access to this page.');
        }

        $title = 'Admin Roles';
        if ($request->ajax()) {
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 100);

            $baseQuery = DB::table('users as u')
                ->leftjoin('usermetas', 'usermetas.user_id', '=', 'u.id')
                ->leftJoin('regions as r', 'r.id', '=', 'usermetas.region_id')
                ->leftJoin('zones as z', 'z.id', '=', 'usermetas.zone_id')
                ->select(
                    'u.id',
                    'u.name as superadmin_name',
                    'u.email',
                    'u.phone',
                    'u.position',
                    'usermetas.gender',
                    DB::raw("COALESCE(r.name, 'All') as region_id"),
                    DB::raw("COALESCE(z.name, 'All') as zone_id"),
                    DB::raw("CASE WHEN u.status = 1 THEN 'Active' ELSE 'Inactive' END as status"),
                    DB::raw("
                        CASE
                            WHEN usermetas.designation = 2 THEN 'Super Admin'
                            WHEN usermetas.designation = 5 THEN 'Regional Head'
                            WHEN usermetas.designation = 6 THEN 'Zonal Head'
                            WHEN usermetas.designation = 7 THEN 'Sports Coordinator'
                            ELSE 'N/A'
                        END as designation
                    "),
                )
                ->where('role_id', 2)->where('u.status', 1);              

            $query = clone $baseQuery;

            if ($request->filled('status')) {
                $query->where('u.status', $request->status);
            }

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%")
                    ->orWhere('u.phone', 'like', "%{$search}%")
                    ->orWhere('r.name', 'like', "%{$search}%")
                    ->orWhere('z.name', 'like', "%{$search}%");
                });
            }
            
            $orderIndex = $request->input('order.0.column');
            $orderDir   = $request->input('order.0.dir', 'asc');
            $orderCol   = $request->input("columns.$orderIndex.data");

            $sortable = [
                'superadmin_name' => 'u.name',
                'email' => 'u.email',
            ];

            if (isset($sortable[$orderCol])) {
                $query->orderBy($sortable[$orderCol], $orderDir);
            }

            $recordsTotal    = $baseQuery->count();
            $recordsFiltered = $query->count();

            if ($length !== -1) {
                $query->skip($start)->take($length);
            }

            return response()->json([
                'draw'            => (int) $request->draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $query->get(),
            ]);
        }
        $regions = DB::table('regions')->get();

        return view('icse.add-super-admin', compact('title', 'regions'));
    }

    // add super admin 
    public function addSuperAdmin(Request $request){
        
        try{
            $validator = Validator::make($request->all(), [
                'superadmin_name'      => 'required|string|max:255',
                'superadmin_email'     => 'required|email|unique:users,email',
                'superadmin_phone'     => 'required',
                'gender'               => 'required|string|max:255',
                // 'password'             => 'required|min:8|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ]);
            }

            $regionId = null;
            $zoneId   = null;

            if (!empty($request->region)) {            
                list($regionName, $regionId) = explode('|', $request->region);
            }

            if (!empty($request->zone)) {

                list($zoneName, $zoneId) = explode('|', $request->zone);
            }

            $lastFiveDigits = substr(preg_replace('/\D/', '', $request->superadmin_phone), -5);
            
            $password = 'Admin@' . $lastFiveDigits;

            // dd($request->all());

            $userId = DB::table('users')->insertGetId([
                'name'       => $request->superadmin_name,
                'email'      => $request->superadmin_email,
                'phone'      => $request->superadmin_phone,
                'userid'     => $request->superadmin_email,
                'password'   => Hash::make($password),
                'position'   => 'Superadmin',
                'role_id'    => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('usermetas')->insert([
                'user_id' => $userId,
                'gender' => $request->gender,
                'region_id' => $regionId,
                'zone_id' => $zoneId,
                'designation' => $request->designation,
                'created_by' => Auth::user()->id,
            ]);
            
            return response()->json([
                'status'  => 1,
                'message' => 'Admin Role added successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // edit super admin 

    public function getSuperAdmin(Request $request){

        try {
            $user = DB::table('users')
                ->leftJoin('usermetas', 'users.id', '=', 'usermetas.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'usermetas.gender',
                    'usermetas.region_id',
                    'usermetas.zone_id',
                    'usermetas.designation',
                )
                ->where('users.id', $request->id)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'User not found'
                ]);
            }
            return response()->json([
                'status' => 1,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }

    // update super admin profile 

    public function updateSuperAdmin(Request $request){

        $validator = Validator::make($request->all(), [
            'id'                => 'required|exists:users,id',
            'superadmin_name'   => 'required|string|max:255',
            'superadmin_email'  => 'required|email|unique:users,email,' . $request->id,
            'superadmin_phone'  => 'required',
            'gender'            => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors()
            ]);
        }
        try {

            $lastFiveDigits = substr(preg_replace('/\D/', '', $request->superadmin_phone), -5);            
            $password = 'Admin@' . $lastFiveDigits;

            DB::table('users')
                ->where('id', $request->id)
                ->update([
                    'name'       => $request->superadmin_name,
                    'email'      => $request->superadmin_email,
                    'phone'      => $request->superadmin_phone,
                    'userid'     => $request->superadmin_email,
                    'password'   => Hash::make($password),
                    'updated_at' => now(),
                ]);
            DB::table('usermetas')
                ->where('user_id', $request->id)
                ->update([
                    'gender' => $request->gender,
                ]);

            return response()->json([
                'status'  => 1,
                'message' => 'Admin Role updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // delete superadmin 

    public function deleteSuperAdmin(Request $request){

        try {
            DB::table('users')
                ->where('id', $request->id)
                ->delete();

            DB::table('usermetas')
                ->where('user_id', $request->id)
                ->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Admin Role deleted successfully!'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Export Admin Credentials
    public function exportCredentials(Request $request) {

	    $userIds = DB::table('users')
        ->where('role_id', 2)
        ->where('status', 1)
        ->pluck('id')
        ->toArray();

	    if(empty($userIds)) {

	        return response()->json(['message' => 'No Active Admins found'], 400);
	    }

	    $timestamp = Carbon::now()->format('Ymd_His');
		$fileName = "admin-credentials_{$timestamp}.xlsx";
		return Excel::download(new AdminCredentials($userIds), $fileName);
        
	}
    
    // Login As school 
    
    public function loginAsSchool(Request $request){
        $userId = DB::table('school_reference')
            ->where('school_id', $request->schoolId)
            ->value('school_user_id');
        
        $school = DB::table('schools')->where('id', $request->schoolId)->first();

        $user = User::where('id', $userId)
            ->where('role_id', 4)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'School user not found.'
            ]);
        }

        // Check status like normal login
        if ($user->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Account inactive.'
            ]);
        }

        if ($user->status == 2) {
            return response()->json([
                'success' => false,
                'message' => 'Account rejected.'
            ]);
        }

        // Save current CISCE user
        session([
            'Auth_id'             => Auth::id(),
            'School_name'         => $school->school_name,
            'impersonate_guard'   => 'web',
            'impersonated_user_id'=> $user->id,
            'is_impersonating'    => true,
        ]);

        // Login as school
        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        AuditHelper::log(
            'impersonation',
            'Admin '.session('Auth_id').' logged in as user id '.$userId
        );

        $cookie = cookie(
            'my_cookie_dot',
            $this->generateRandomString(),
            60,
            '/',
            config('app.cookie_domain'),
            true,
            true,
            false,
            'None'
        );

        return response()->json([
            'success' => true,
            'redirect_url' => route('filldart.dashboard')
        ])->cookie($cookie);
    }

    private function generateRandomString($length = 5){

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for($i = 0; $i < $length; $i++) 
		{
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}

		return $randomString;
	}

    public function leaveSchool(Request $request){
        try {

            $superAdminId = session('Auth_id');

            if (!$superAdminId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No impersonation session found.'
                ]);
            }

            $schoolId = Auth::id();

            // Remove impersonation data first
            session()->forget([
                'Auth_id',
                'School_name',
                'impersonate_guard',
                'impersonated_user_id',
                'is_impersonating'
            ]);
            AuditHelper::log(
                'impersonation',
                'Admin '.$superAdminId.' logged out as user id '.$schoolId
            );

            Auth::logout();

            Auth::loginUsingId($superAdminId);

            $request->session()->regenerate();

            $cookie = cookie(
                'my_cookie_dot',
                $this->generateRandomString(),
                60,
                '/',
                config('app.cookie_domain'),
                true,
                true,
                false,
                'None'
            );

            return response()->json([
                'success'      => true,
                'redirect_url' => route('cicse.schoollist')
            ])->cookie($cookie);

        } catch (\Exception $e) {

            \Log::error('Leave Impersonation Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Methods for data restoration.
     */
    public function DeletedStudent(Request $request) {

        // 1. Build the absolute base query structure (Unfiltered)
        $baseQuery = DeletedStudent::where('restored','=', '0')
            ->join('schools', 'schools.id', '=', 'deleted_students.school_id')
            ->leftJoin('users', 'users.id', '=', 'deleted_students.deleted_by')
            ->select(
                'schools.school_code',
                'schools.school_name',
                'schools.id as school_id',
                'users.name as deleted_by_username',
                DB::raw('count(*) as total_deleted')
            )
            ->groupBy(
                'deleted_students.school_id', 
                'deleted_students.deleted_by', 
                'schools.school_code', 
                'schools.school_name', 
                'users.name'
            );

        // 2. Handle Server-Side DataTables AJAX Request
        if ($request->ajax()) {

            $queryForTotalCount = clone $baseQuery;
            
            $recordsTotal = DB::table(DB::raw("({$queryForTotalCount->toSql()}) as sub"))
                ->mergeBindings($queryForTotalCount->getQuery()) ->count();

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $baseQuery->where(function ($q) use ($search) {
                    $q->where('schools.school_code', 'like', "%{$search}%")
                      ->orWhere('schools.school_name', 'like', "%{$search}%")
                      ->orWhere('users.name', 'like', "%{$search}%");
                });
            }
            
            // Clone for filtered counts and final chunk payload extraction
            $queryForFilteredCount = clone $baseQuery;
            $queryForData          = clone $baseQuery;

            // Calculate total rows matching search queries
            $recordsFiltered = DB::table(DB::raw("({$queryForFilteredCount->toSql()}) as sub"))
                ->mergeBindings($queryForFilteredCount->getQuery())
                ->count();

            // 4. Apply Pagination Limits
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 100);

            if ($length !== -1) {
                $queryForData->skip($start)->take($length);
            }

            // 5. Execute Query ->get() FIRST, then ->map() on the resulting collection
            $rawRecords = $queryForData->get();



            $flattenedList = $rawRecords->map(function ($item) {

                $encryptedId = Crypt::encryptString($item->school_id);
                return [
                    'id'                  => $item->school_id,
                    'school_code'         => $item->school_code,
                    'school_name'         => $item->school_name,
                    'deleted_by_username' => $item->deleted_by_username ?? 'System Admin',
                    'total_deleted'       => $item->total_deleted ?? 0,
                    'view_link'           => route('admin.restore.school', ['school_id' => $encryptedId]),
                ];
            });

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $flattenedList,
            ]);
        }

        // 6. Fallback for initial structural Page Load (Non-AJAX)
        $schools = $baseQuery->get();
        $title = 'Deleted Student';

        return view('icse.restoration.index', compact('schools', 'title'));
    }

    public function showSchoolRecords(Request $request, $schoolId) {
        $schoolId = Crypt::decryptString($schoolId);

        $school_code = School::find($schoolId);
        $title = $school_code->school_code. ' | '. $school_code->school_name;

        // 1. Base query for this school's records
        $baseQuery = DeletedStudent::where('school_id', $schoolId)->where('restored','=', '0');

        // 2. Handle DataTables Server-Side Requests
        if ($request->ajax()) {
            
            $recordsTotal = (clone $baseQuery)->count();

            // Apply Search Filter on base table columns
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $baseQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('student_uid', 'like', "%{$search}%");
                });
            }

            $recordsFiltered = (clone $baseQuery)->count();

            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);

            if ($length !== -1) {
                $baseQuery->skip($start)->take($length);
            }

            $records = $baseQuery->get();

            $customClassIds = $records->map(function ($record) {
                return $record->json_data['custom_class_id'] ?? $record->custom_class_id;
            })->filter()->unique()->toArray();

            $classMappings = [];
            if (!empty($customClassIds)) {
                $classMappings = DB::table('custom_classes')
                    ->join('class', 'class.id', '=', 'custom_classes.class_id')
                    ->whereIn('custom_classes.id', $customClassIds)
                    ->select(
                        'custom_classes.id as custom_class_id',
                        'class.name as class_name',
                        'custom_classes.section'
                    )
                    ->get()
                    ->keyBy('custom_class_id')
                    ->toArray();
            }

            // 4. Flatten Payload Collection for Datatables View Execution
            $flattenedData = $records->map(function ($record) use ($classMappings, $school_code) {
                $jsonPayload = $record->json_data ?? [];
                $targetCustomClassId = $jsonPayload['custom_class_id'] ?? $record->custom_class_id;

                // Resolve mapped class and section names safely
                $className = 'N/A';
                $sectionName = 'N/A';

                if ($targetCustomClassId && isset($classMappings[$targetCustomClassId])) {
                    $className = $classMappings[$targetCustomClassId]->class_name;
                    $sectionName = $classMappings[$targetCustomClassId]->section;
                }

                $encryptedId = Crypt::encryptString($record->id);

                return [
                    'id'              => $record->id,
                    'school_code'     => $school_code->school_code,
                    'student_id'      => $record->student_id,
                    'name'            => $record->name ?? ($jsonPayload['student_name'] ?? 'N/A'),
                    'student_uid'     => $record->student_uid ?? ($jsonPayload['student_uid'] ?? 'N/A'),
                    'class_name'      => $className, 
                    'section'         => $sectionName,   
                    'deleted_at'      => $record->deleted_at ? $record->deleted_at->format('Y-m-d H:i:s') : 'N/A',
                    'restore_url'     => route('admin.restore.execute', ['id' => $encryptedId]) 
                ];
            });

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $flattenedData,
            ]);
        }

        return view('icse.restoration.show', compact('schoolId', 'title'));
    }

    public function restore(Request $request, $id) {
        $id = Crypt::decryptString($id);

        $deletedRecord = DeletedStudent::findOrFail($id);
                
        $school_code = $deletedRecord->school?->school_code;

        $payload = $deletedRecord->json_data;
        $studentUid = $payload['student_uid'] ?? $deletedRecord->student_uid;

        if (!$studentUid) {
            return redirect()->back()->with('error', 'Invalid record: student_uid missing.');
        }

        $existingStudent = DB::table('students')->where('student_uid', $studentUid)->first();

        if ($existingStudent) {
            return redirect()->back()->with('warning_uid', [
                'message' => "A student with UID '{$studentUid}' ({$existingStudent->student_name}) already exists in active records!",
                'uid' => $studentUid
            ]);
        }

        $CustomClassData = CustomClass::find($payload['custom_class_id']);


        try {
            
            DB::transaction(function () use ($deletedRecord, $payload, $CustomClassData, $school_code) {
                
                DB::table('students')->insert([
                    'id'                 => $payload['id'], 
                    'school_id'          => $payload['school_id'] ?? $deletedRecord->school_id,
                    'school_code'        => $payload['school_code'] ?? $school_code,
                    'student_uid'        => $payload['student_uid'],
                    'student_name'       => $payload['student_name'] ?? $deletedRecord->name,
                    'custom_class_id'    => $payload['custom_class_id'] ?? $deletedRecord->custom_class_id,
                    
                    'gender'             => $payload['gender'] ?? 'Unknown', 
                    'class_id'           => $CustomClassData->class_id,
                    'section_id'         => $CustomClassData->section,
                    'dob'                => $payload['dob'] ?? null,
                    'user_id'            => $payload['user_id'] ?? trim($school_code) . $payload['student_uid'], 
                    'password'           => $payload['password'] ?? null,
                    'email_id'           => $payload['email_id'] ?? null,

                    'rollno'             => $payload['rollno'] ?? null,
                    'academic_year'     => $payload['academic_year'] ?? '2025-2026',
                    'is_pwd'             => $payload['is_pwd'] ?? null,
                    'apaarId'            => $payload['apaarId'] ?? 'N.A.',
                    'password_generated' => 0,
                    'status'             => 'active',
                    'created_at'         => $payload['created_at'] ?? now(),
                    'updated_at'         => $payload['updated_at'] ?? now(),
                ]);

                $deletedRecord->update([
                    'restored' => '1',
                    'restoration_date' => now()
                ]);
            });

            return redirect()->back()->with('success', 'Student record restored successfully!');

        } catch (\Exception $e) {

            \Log::error('Restoration Failed: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Restoration failed: ' . $e->getMessage());
        }
    }


    public function RestoreStudents(Request $request)  {

        $action = $request->input('action');
        $ids = $request->input('ids', []); 

        $schoolCode = School::find($request->input('schoolid'))->school_code;

        if (empty($ids) || !in_array($action, ['restore', 'trash'])) {
            return response()->json(['status' => false, 'message' => 'Invalid request data or action.'], 400);
        }

        
        if ($action === 'restore') {
            
            $archivedRecords = DeletedStudent::whereIn('student_id', $ids)->get();
            if ($archivedRecords->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No records found to restore.'], 404);
            }

            $uidsToCheck = $archivedRecords->map(function ($rec) {
                return $rec->json_data['student_uid'] ?? $rec->student_uid;
            })->filter()->toArray();

            $conflictingUids = DB::table('students')->whereIn('student_uid', $uidsToCheck)->pluck('student_uid')->toArray();

            if (!empty($conflictingUids)) {

                return response()->json([
                    'status' => false, 
                    'message' => 'Restoration blocked! Student Already Exist for Admission No.: ' . implode(', ', $conflictingUids). 'To restore their deleteed records ,please delete their existing profiles records first, then try again.'
                ], 422);
            }

            $customClassIds = $archivedRecords->map(function ($rec) {
                return $rec->json_data['custom_class_id'] ?? $rec->custom_class_id;
            })->filter()->unique()->toArray();

            $classMappings = [];
            if (!empty($customClassIds)) {
                $classMappings = DB::table('custom_classes')
                    ->whereIn('id', $customClassIds)
                    ->get()
                    ->keyBy('id')
                    ->toArray();
            }

            $restoredCount = 0;

            try {
                DB::transaction(function () use ($archivedRecords, $classMappings, &$restoredCount, $schoolCode) {
                    foreach ($archivedRecords as $record) {
                        
                        $payload = $record->json_data ?? [];
                        $targetCustomClassId = $payload['custom_class_id'] ?? $record->custom_class_id;

                        $fallbackClassId = 0;
                        $fallbackSectionId = 'A';

                        if ($targetCustomClassId && isset($classMappings[$targetCustomClassId])) {
                            $fallbackClassId = $classMappings[$targetCustomClassId]->class_id ?? 0;
                            $fallbackSectionId = $classMappings[$targetCustomClassId]->section ?? 'A';
                        }

                        $studentUid = $payload['student_uid'] ?? $record->student_uid;

                        DB::table('students')->insert([
                            'id'                 => $payload['id'] ?? $record->student_id, 
                            'school_id'          => $payload['school_id'] ?? $record->school_id,
                            'school_code'        => $schoolCode,
                            'student_uid'        => $studentUid,
                            'student_name'       => $payload['student_name'] ?? $record->name,
                            'custom_class_id'    => $targetCustomClassId,
                            'gender'             => $payload['gender'] ?? 'Unknown', 
                            'class_id'           => $payload['class_id'] ?? $fallbackClassId,
                            'section_id'         => $payload['section_id'] ?? $fallbackSectionId,
                            'dob'                => $payload['dob'] ?? null,
                            'user_id'            => $payload['user_id'] ?? (trim($schoolCode) . $studentUid), 
                            'password'           => $payload['password'] ?? null,
                            'email_id'           => $payload['email_id'] ?? null,
                            'rollno'             => $payload['rollno'] ?? null,
                            'academic_year'      => $payload['academic_year'] ?? '2025-2026',
                            'is_pwd'             => $payload['is_pwd'] ?? null,
                            'apaarId'            => $payload['apaarId'] ?? 'N.A.',
                            'password_generated' => 0,
                            'status'             => 'active',
                            'created_at'         => $payload['created_at'] ?? now(),
                            'updated_at'         => now(),
                        ]);

                        // $record->delete();
                        $restoredCount++;
                    }
                });

                if (class_exists('AuditHelper')) {
                    AuditHelper::log('action', "Restored Batch of {$restoredCount} Students");
                }

                return response()->json([
                    'status'  => true,
                    'count'   => $restoredCount,
                    'message' => "{$restoredCount} student record(s) restored back to active registration successfully."
                ]);

            } catch (\Exception $e) {
                \Log::error('Bulk Restoration Failed: ' . $e->getMessage());
                return response()->json([
                    'status'  => false, 
                    'message' => 'Restoration failed due to engine error: ' . $e->getMessage()
                ], 500);
            }
        }

    }

    public function AuditLogs(Request $request) {

        $data = AuditLogs::where('user_id',3257)->orderBy('created_at','desc')->get();
       // echo "<pre>"; print_r($data); exit();

        $title = 'Audict Logs';
        return view('icse.audit_logs', compact('title','data'));

    }

    //report email.
    public function bulkReportEmailView(Request $request){

        $title = "Send Bulk Email";
        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;
        
        if ($request->ajax()) {

            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 100);

            $baseQuery = DB::table('schools_assessment as s')
                ->where('s.academic_year', $academicYear)
                ->where('s.registered_students','!=', 0)
                ->whereNotIn('s.school_code', ['AK001', 'AK003']);
            
            $query = clone $baseQuery;
            $query->select(
                's.school_id',
                's.school_code',
                's.school_name',
                's.region',
                's.zonename',
                's.registered_students',
                's.completed',
                's.ongoing',
                's.yet_to_start',
                's.email_sent'
            );

            if ($request->filled('region')) {
                $regionData = explode('|', $request->region);
                $regionName = $regionData[0];
                $regionId   = $regionData[1] ?? null;
                $query->whereRaw('LOWER(s.region) = ?', [strtolower($regionName)]);
            }

            if($request->filled('zone')){
                $query->where('s.zonename', $request->zone);
            }
            if ($request->filled('status')) {

                if ($request->status === 'completed') {
                    $query->whereColumn('s.completed', 's.registered_students')
                    ->where('s.registered_students', '>', 0);
                } elseif ($request->status === 'ongoing') {
                    $query->where('s.ongoing', '>', 0);
                } elseif ($request->status === 'yet_to_start') {
                    $query->whereColumn('s.yet_to_start', 's.registered_students');
                }
            }

            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('s.school_code', 'like', "%{$search}%")
                    ->orWhere('s.school_name', 'like', "%{$search}%")
                    ->orWhere('s.region', 'like', "%{$search}%");
                });
            }

            $orderIndex = $request->input('order.0.column');
            $orderDir   = strtolower($request->input('order.0.dir', 'desc'));

            $sortable = [
                1 => 's.school_code',
                2 => 's.school_name',
                3 => 's.region',
                4 => 's.zonename',
                5 => 's.registered_students',
                6 => 's.completed',
                7 => 's.ongoing',
                8 => 's.yet_to_start',
            ];

            if (
                isset($sortable[$orderIndex]) &&
                in_array($orderDir, ['asc', 'desc'], true)
            ) {
                $query->orderBy($sortable[$orderIndex], $orderDir);
            } else {
                // Default sorting: Completed DESC
                $query->orderBy('s.completed', 'desc');
            }

            $recordsTotal = (clone $baseQuery)->count();

            $recordsFiltered = (clone $query)
                ->reorder()
                ->count();

            if ($length !== -1) {
                $query->skip($start)->take($length);
            }

            return response()->json([
                'draw'            => (int) $request->draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $query->get(),
            ]);
        }

        $region = Region::where('status', 1)->orderBy('name')->get();
        $zones = DB::table('zones')->orderBy('name')->get();
        

        return view('icse.email.bulk-email', compact('title', 'region','zones'));
    }

    public function validateEmail(Request $request){

        $year = date('Y');
        $month = date('m');

        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

        $schoolId = $request->school_id;

        $students = DB::table('students')
            ->where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->where('status', 'active')
            ->get([
                'id',
                'email_id'
            ]);

        $totalStudents = $students->count();

        $allStudentIds = [];
        $validStudentIds = [];
        $invalidStudentIds = [];
        $domainResults = [];

        foreach ($students as $student) {

            $allStudentIds[] = $student->id;

            $email = trim($student->email_id ?? '');

            // 1. Empty email
            if ($email === '') {
                $invalidStudentIds[] = $student->id;
                continue;
            }

            // 2. Email format validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalidStudentIds[] = $student->id;
                continue;
            }

            // 3. Extract domain
            $atPosition = strrpos($email, '@');

            if ($atPosition === false) {
                $invalidStudentIds[] = $student->id;
                continue;
            }

            $domain = strtolower(substr($email, $atPosition + 1));

            // 4. Check DNS only once per domain
            if (!array_key_exists($domain, $domainResults)) {
                $domainResults[$domain] = checkdnsrr($domain, 'MX');
            }

            if (!$domainResults[$domain]) {
                $invalidStudentIds[] = $student->id;
                continue;
            }

            // 5. Valid student
            $validStudentIds[] = $student->id;
        }

        $invalidCount = count($invalidStudentIds);
        $validCount = count($validStudentIds);

        return response()->json([
            'status' => true,
            'total' => $totalStudents,
            'valid' => $validCount,
            'invalid' => $invalidCount,
            'allStudentIds' => $allStudentIds,
            'validStudentIds' => $validStudentIds,
            'invalidStudentIds' => $invalidStudentIds,
        ]);
    }

    public function downloadInvalidEmails(Request $request){
       
       $studentIds = $request->input('student_ids', []);
       $title = $request->title;

        if (empty($studentIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No invalid students found.'
            ], 400);
        }
        $students = DB::table('students')
			->join('custom_classes','students.custom_class_id', '=','custom_classes.id')
            ->join('class', 'custom_classes.class_id', '=', 'class.id')
			->select(
				'students.school_code',
				'students.student_name',
				'students.rollno',
				'students.gender',
				'students.email_id',
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
            ->whereIn('students.id', $studentIds)
			->get();

        $emailDetails = [];
        if($title == "Invalid"){
            $domainResults = [];
            foreach ($students as $student) {
                $email = trim($student->email_id ?? '');
                if ($email === '') {
                    $emailDetails[] = [
                        'school_code'  => $student->school_code,
                        'student_name' => $student->student_name,
                        'class'        => $student->classname,
                        'section'      => $student->section,
                        'rollno'       => $student->rollno,
                        'email'        => '',
                        'reason'       => 'Email address is missing.',
                    ];
                    continue;
                }
                // 2. Email format validation
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailDetails[] = [
                        'school_code'  => $student->school_code,
                        'student_name' => $student->student_name,
                        'class'        => $student->classname,
                        'section'      => $student->section,
                        'rollno'       => $student->rollno,
                        'email'        => $email,
                        'reason'       => "Invalid email format: {$email}.",
                    ];
                    continue;
                }

                // 3. Get email domain
                $atPosition = strrpos($email, '@');
                if ($atPosition === false) {
                    $emailDetails[] = [
                        'school_code'  => $student->school_code,
                        'student_name' => $student->student_name,
                        'class'        => $student->classname,
                        'section'      => $student->section,
                        'rollno'       => $student->rollno,
                        'email'        => $email,
                        'reason'       => "Invalid email format: {$email}.",
                    ];
                    continue;
                }

                $domain = strtolower(substr($email, $atPosition + 1));
                // 4. Check MX record only once per domain
                if (!array_key_exists($domain, $domainResults)) {
                    $domainResults[$domain] = checkdnsrr($domain, 'MX');
                }
                // 5. Invalid domain
                if (!$domainResults[$domain]) {

                    $emailDetails[] = [
                        'school_code'  => $student->school_code,
                        'student_name' => $student->student_name,
                        'class'        => $student->classname,
                        'section'      => $student->section,
                        'rollno'       => $student->rollno,
                        'email'        => $email,
                        'reason'       => "Invalid email domain '{$domain}'. The domain does not have a valid mail server.",
                    ];
                    continue;
                }
            }
        }else{
            foreach ($students as $student) {
                $email = trim($student->email_id ?? '');
                $emailDetails[] = [
                    'school_code'  => $student->school_code,
                    'student_name' => $student->student_name,
                    'class'        => $student->classname,
                    'section'      => $student->section,
                    'rollno'       => $student->rollno,
                    'email'        => $email,
                    'reason'       => '',
                ];
            }
        }

        $timestamp = Carbon::now()->format('Ymd_His');
		$fileName = "email_validation_{$timestamp}.xlsx";
		return Excel::download(new InvalidEmailsExport($emailDetails, $title), $fileName);

    }


    public function sendBulkEmail(Request $request){

        try{
            
            $year = date('Y');
            $month = date('m');
            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

            $schoolId = $request->schoolId;
            $studentIds = $request->studentIds; 
            $school = School::find($schoolId);
            $TermMasterId = $this->getTermId($schoolId);
            $action = $request->input('action');

            $completedStudents = DB::table('students')
                ->leftJoin('SeniorTestResultsSummary as senior', function ($join) use ($TermMasterId) {
                    $join->on('students.id', '=', 'senior.student_id')
                        ->on('students.school_id', '=', 'senior.school_id')
                        ->where('senior.term_id', '=', $TermMasterId);
                })
                ->leftJoin('LowerTestResultsSummary as lower', function ($join) use ($TermMasterId) {
                    $join->on('students.id', '=', 'lower.student_id')
                        ->on('students.school_id', '=', 'lower.school_id')
                        ->where('lower.term_id', '=', $TermMasterId);
                })
                ->leftJoin('CwsnTestResultSummary as cwsn', function ($join) use ($TermMasterId) {
                    $join->on('students.id', '=', 'cwsn.student_id')
                        ->on('students.school_id', '=', 'cwsn.school_id')
                        ->where('cwsn.term_id', '=', $TermMasterId);
                })
                ->where('students.school_code', $school->school_code)
                ->where('students.academic_year', $academicYear)
                ->whereIn('students.id', $studentIds)
                // ->where('students.email_flag', '0')
                ->where(function ($query) {

                    // CWSN students: at least 4 assessment fields must be non-empty
                    $query->where(function ($q) {
                        $q->whereIn('students.class_id', $this->cwsnClasses)
                            ->where('students.is_pwd', '1')
                            ->whereRaw("
                                (
                                    CASE WHEN cwsn.`20m_pacer` IS NOT NULL AND cwsn.`20m_pacer` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`15m_pacer` IS NOT NULL AND cwsn.`15m_pacer` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`1mile_run_walk` IS NOT NULL AND cwsn.`1mile_run_walk` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`shoulder_stretch` IS NOT NULL AND cwsn.`shoulder_stretch` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`sit_and_reach` IS NOT NULL AND cwsn.`sit_and_reach` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`modified_apley_test` IS NOT NULL AND cwsn.`modified_apley_test` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`curlup` IS NOT NULL AND cwsn.`curlup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`modified_curlup` IS NOT NULL AND cwsn.`modified_curlup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`dumbbell_press` IS NOT NULL AND cwsn.`dumbbell_press` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`pullup` IS NOT NULL AND cwsn.`pullup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`pushup` IS NOT NULL AND cwsn.`pushup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`seated_pushup` IS NOT NULL AND cwsn.`seated_pushup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`trunk_lift` IS NOT NULL AND cwsn.`trunk_lift` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`Isometric_pushup` IS NOT NULL AND cwsn.`Isometric_pushup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`reverse_curl` IS NOT NULL AND cwsn.`reverse_curl` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`modified_pullup` IS NOT NULL AND cwsn.`modified_pullup` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`40m_push_walk` IS NOT NULL AND cwsn.`40m_push_walk` <> '' THEN 1 ELSE 0 END +
                                    CASE WHEN cwsn.`cwsn_bmi` IS NOT NULL AND cwsn.`cwsn_bmi` <> '' THEN 1 ELSE 0 END
                                ) >= 4
                            ");
                    })

                    // Higher classes: all specified senior fields must be non-empty
                    ->orWhere(function ($q) {
                        $q->whereIn('students.class_id', $this->higherClasses)
                            ->whereNotNull('senior.sit_and_reach')
                            ->where('senior.sit_and_reach', '<>', '')
                            ->whereNotNull('senior.run_600m')
                            ->where('senior.run_600m', '<>', '')
                            ->whereNotNull('senior.pushups')
                            ->where('senior.pushups', '<>', '')
                            ->whereNotNull('senior.dash_50m')
                            ->where('senior.dash_50m', '<>', '')
                            ->whereNotNull('senior.curlup')
                            ->where('senior.curlup', '<>', '')
                            ->whereNotNull('senior.bmi')
                            ->where('senior.bmi', '<>', '')
                            ->whereNotNull('senior.height')
                            ->where('senior.height', '<>', '')
                            ->whereNotNull('senior.weight')
                            ->where('senior.weight', '<>', '');
                    })

                    // Lower classes: all specified lower fields must be non-empty
                    ->orWhere(function ($q) {
                        $q->whereNotIn('students.class_id', array_merge(
                            $this->higherClasses,
                            $this->cwsnClasses
                        ))
                            ->whereNotNull('lower.running')
                            ->where('lower.running', '<>', '')
                            ->whereNotNull('lower.hopping')
                            ->where('lower.hopping', '<>', '')
                            ->whereNotNull('lower.jumping_landing')
                            ->where('lower.jumping_landing', '<>', '')
                            ->whereNotNull('lower.skipping')
                            ->where('lower.skipping', '<>', '')
                            ->whereNotNull('lower.dodging')
                            ->where('lower.dodging', '<>', '')
                            ->whereNotNull('lower.one_foot_balance')
                            ->where('lower.one_foot_balance', '<>', '')
                            ->whereNotNull('lower.beam_walk')
                            ->where('lower.beam_walk', '<>', '')
                            ->whereNotNull('lower.catching_receiving_bounce')
                            ->where('lower.catching_receiving_bounce', '<>', '')
                            ->whereNotNull('lower.catching_small_ball')
                            ->where('lower.catching_small_ball', '<>', '')
                            ->whereNotNull('lower.under_arm_throw')
                            ->where('lower.under_arm_throw', '<>', '')
                            ->whereNotNull('lower.over_arm_throw')
                            ->where('lower.over_arm_throw', '<>', '')
                            ->whereNotNull('lower.striking_drop_hit')
                            ->where('lower.striking_drop_hit', '<>', '')
                            ->whereNotNull('lower.dribbling_hands')
                            ->where('lower.dribbling_hands', '<>', '')
                            ->whereNotNull('lower.dribbling_feet')
                            ->where('lower.dribbling_feet', '<>', '')
                            ->whereNotNull('lower.kicking_ball')
                            ->where('lower.kicking_ball', '<>', '')
                            ->whereNotNull('lower.flamingo_balance')
                            ->where('lower.flamingo_balance', '<>', '')
                            ->whereNotNull('lower.plate_tapping')
                            ->where('lower.plate_tapping', '<>', '')
                            ->whereNotNull('lower.bmi')
                            ->where('lower.bmi', '<>', '')
                            ->whereNotNull('lower.height')
                            ->where('lower.height', '<>', '')
                            ->whereNotNull('lower.weight')
                            ->where('lower.weight', '<>', '');
                    });
                })
                ->select('students.id', 'students.email_id')
                ->distinct()
                ->get();

            $completedStudentIds = $completedStudents->pluck('id')->toArray();
            
            $report_batch = EmailReportBatch::create([
                'school_id' => $schoolId,
                'status' => 'pending',
            ]);

            foreach ($completedStudents as $student) {            	
                EmailReportRequest::insert(
                    [
                        'batch_id' => $report_batch->id,
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'email_id' => $student->email_id,
                        'status' => 'requested',
                        'requested_at' => now(),
                    ]
                );
            }			

            GenerateSchoolReportsMasterJob::dispatch($schoolId, $completedStudentIds, $TermMasterId, $report_batch->id, $action)->onQueue('report_generation');
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
    
}
