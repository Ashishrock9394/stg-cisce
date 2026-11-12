<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Sstudent;
use App\Models\Sclass;
use App\Models\CustomClass;
use Carbon\Carbon;
use DB;

class Fitness365Controller extends Controller
{

    public function stateWiseHealthData()
    {
        try {

            $stateData = DB::table('schools')
                ->leftJoin('students as s', 's.school_id', '=', 'schools.id')
                ->leftJoin('SeniorTestResults as str', function ($join) {
                    $join->on('str.StudentID', '=', 's.id')
                        ->where('str.TestTypeID', 18);
                })
                ->groupBy('schools.state')
                ->select(
                    'schools.state as name',

                    DB::raw('COUNT(DISTINCT schools.id) as schools'),
                    DB::raw('COUNT(DISTINCT s.id) as students'),

                    DB::raw("SUM(str.level = 'UW') as UW"),
                    DB::raw("SUM(str.level = 'N') as N"),
                    DB::raw("SUM(str.level = 'OW') as OW"),
                    DB::raw("SUM(str.level = 'OB') as OB")
                )
                ->orderBy('schools.state')
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'stateData' => $stateData,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function stateData(){
        try {
            $year = date('Y');
            $month = date('m');
            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

            $studentsSub = DB::table('students')
                ->where('academic_year', $academicYear)
                ->select('school_id', DB::raw('COUNT(*) as total_students'))
                ->groupBy('school_id');

            $trainersSub = DB::table('school_trainers')
                ->select('school_id', DB::raw('COUNT(DISTINCT trainer_id) as total_trainers'))
                ->groupBy('school_id');

            $stateData = DB::table('schools')
                ->leftJoinSub($studentsSub, 's', function ($join) {
                    $join->on('s.school_id', '=', 'schools.id');
                })
                ->leftJoinSub($trainersSub, 't', function ($join) {
                    $join->on('t.school_id', '=', 'schools.id');
                })
                ->groupBy('schools.state')
                ->select(
                    'schools.state as name',
                    DB::raw('COUNT(DISTINCT schools.id) as schools'),
                    DB::raw('SUM(COALESCE(t.total_trainers, 0)) as trainers'),
                    DB::raw('SUM(COALESCE(s.total_students, 0)) as students')
                )
                ->orderBy('schools.state')
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'stateData' => $stateData,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function stateDetails($stateCode){
        try {
            $schoolIds = DB::table('schools')
            ->join('states', 'states.name', '=', 'schools.state')
            ->where('states.code', $stateCode)
            ->pluck('schools.id');

            $year = date('Y');
            $month = date('m');
            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

            $schoolsDetail = DB::table('schools_assessment')
            ->where('academic_year', $academicYear)
            ->whereIn('school_id', $schoolIds)
            ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'stateDetails' => $schoolsDetail,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }


    public function districtWiseHealthData(){
        try {

            $districtData = DB::table('schools')
                ->leftJoin('students as s', 's.school_id', '=', 'schools.id')
                ->leftJoin('SeniorTestResults as str', function ($join) {
                    $join->on('str.StudentID', '=', 's.id')
                        ->where('str.TestTypeID', 18);
                })
                ->groupBy('schools.district')
                ->select(
                    'schools.district as name',

                    DB::raw('COUNT(DISTINCT schools.id) as schools'),
                    DB::raw('COUNT(DISTINCT s.id) as students'),

                    DB::raw("SUM(str.level = 'UW') as UW"),
                    DB::raw("SUM(str.level = 'N') as N"),
                    DB::raw("SUM(str.level = 'OW') as OW"),
                    DB::raw("SUM(str.level = 'OB') as OB")
                )
                ->orderBy('schools.district')
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'districtData' => $districtData,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function nationalFitnessSeries(){

        $levelColors = [
            'L1'=>'#fe4a5d','L2'=>'#ffaa62','L3'=>'#ffd26e',
            'L4'=>'#74c4d6','L5'=>'#a3d55f','L6'=>'#6bc04b','L7'=>'#00953b'
        ];

        $levels = ['L1','L2','L3','L4','L5','L6','L7'];

        $rows = DB::table('test_level_summary')
            ->select('skill_name','level','total_students')
            ->whereIn('level', $levels)
            ->orderBy('TestTypeID')
            ->get();

        $skillCategories = $rows->pluck('skill_name')->unique()->values()->toArray();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->level][$row->skill_name] = (int) $row->total_students;
        }

        $series = [];
        foreach ($levels as $level) {
            $data = [];

            foreach ($skillCategories as $skill) {
                $data[] = $grouped[$level][$skill] ?? 0;
            }

            $series[] = [
                'name'  => "Level - $level",
                'data'  => $data,
                'color' => $levelColors[$level]
            ];
        }

        return response()->json([
            'levels' => $levels,
            'categories' => $skillCategories,
            'series' => $series
        ]);
    }

}
