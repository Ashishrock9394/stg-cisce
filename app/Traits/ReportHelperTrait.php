<?php  

namespace App\Traits;
use App\Models\TermMaster;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


trait ReportHelperTrait
{

    public function getStudentData($studentId) {
        
        return DB::table('students')
        ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
        ->leftJoin('class', 'students.class_id', '=', 'class.id')
        ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
        ->leftJoin('usermetas', 'usermetas.school_id', '=', 'schools.id')
        ->leftJoin('student_rpwd_mapping', 'student_rpwd_mapping.student_id', '=', 'students.id')
        ->leftJoin('pwd_types', 'pwd_types.id', '=', 'student_rpwd_mapping.pwd_type_id')
        ->leftJoin('pwd_categories', 'pwd_categories.id', '=', 'pwd_types.pwd_cat_id')
        ->select(
            'schools.school_name','schools.school_code','schools.id as schools_id','schools.logo',
            'students.id as student_id','students.student_uid as admissionnumber',
            'students.student_name as student_name','students.gender', 'students.is_pwd', 'pwd_types.disability_type',
            'students.class_id','students.section_id','students.custom_class_id', 'pwd_types.pwd_cat_id', 'pwd_categories.disability_category',
            'pwd_categories.adoptation_note', 'students.dob','students.email_id','students.rollno','students.status','usermetas.signature', 'usermetas.hod_sign',
            DB::raw("CASE 
                WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                THEN custom_classes.nomenclature ELSE class.name END AS display_classname"),
            'custom_classes.section'
        )
        ->where('students.id', $studentId)
        ->first();

    }
    

    public function getTermId($schoolId, $selectedYear = null) {
        if (session()->has('term_id')) {
            return session('term_id');
        }else{
            if(!empty($selectedYear)){
                return TermMaster::where('school_id', $schoolId)
                ->where('academic_year', $selectedYear)
                ->where('is_active', 1)->value('id');
            }
            return TermMaster::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->whereDate('term_start_date', '<=', today())
            ->whereDate('term_end_date', '>=', today())
            ->value('id');
        }
    }
    
    // for current and previous term ids 

    protected function getCurrentAndPreviousTermIds(int $schoolId, int $selectedTermId): array {
        
        $terms = TermMaster::where('school_id', $schoolId)->where('is_active', 1)
            ->orderBy('term_start_date', 'desc')->get(['id', 'term_start_date']);

        $selectedIndex = $terms->search(fn ($term) => $term->id == $selectedTermId);

        if ($selectedIndex === false) {
            return [$selectedTermId];
        }

        $termIds = [$terms[$selectedIndex]->id];

     
        if (isset($terms[$selectedIndex + 1])) {
            $termIds[] = $terms[$selectedIndex + 1]->id;
        }

        return $termIds;
    }

    public function getReportData($studentId, $termIds) {

        return DB::table('SeniorTestResults')
            ->join('skill_reports','skill_reports.id','=','SeniorTestResults.TestTypeId')
            ->join('TestTypeMaster','TestTypeMaster.TestTypeId','=','skill_reports.TestTypeMasterID')
            ->join('TestCategoryMaster','TestCategoryMaster.TestCategoryID','=','TestTypeMaster.TestCategoryID')
            ->join('term_masters','term_masters.id','=', 'SeniorTestResults.TermId')
            ->select(
                'SeniorTestResults.TestTypeID',
                'SeniorTestResults.TermId',
                'SeniorTestResults.created_at',
                'SeniorTestResults.Score',
                'SeniorTestResults.LeftScore',
                'SeniorTestResults.RightScore',
                'skill_reports.skill_name',
                'skill_reports.TestTypeMasterID',
                'SeniorTestResults.weight',
                'SeniorTestResults.height',
                'TestTypeMaster.TestCategoryID',
                'TestTypeMaster.ScoreUnit',
                'TestCategoryMaster.TestCategoryName',
                'TestTypeMaster.ScoreCriteria'
            )
            ->orderBy('SeniorTestResults.created_at', 'desc')
            ->where('StudentID', $studentId)
            ->whereIn('SeniorTestResults.TermId', $termIds)
            ->get();
    }


    public function mapReportData($reportData, $studentAge, $studentGender, $ageGender) {

        $bmibenchMark = DB::table('LP_BMI_List')->where('Age','=', $ageGender)->get()->groupBy(function ($row) {
            return $row->Age;
        });

        return $reportData->map(function ($item) use ($bmibenchMark, $studentAge, $studentGender, $ageGender) {

            $bmiBenchmarkRow = $bmibenchMark->get($ageGender)?->first();            
            $getPerformance = $this->getPerformance($item, $studentAge, $studentGender, $bmiBenchmarkRow);
            $normalizedScore = $this->formatValue($item->Score, $item->ScoreUnit);

            return [
                'Category'   => $item->TestCategoryName,
                'TermId'     => $item->TermId,
                'created_at' => \Carbon\Carbon::parse($item->created_at)->format('d M Y'),
                'Level'      => $getPerformance['level'] ?? '',
                'Test_Name'  => $item->skill_name,
                'score'      => $normalizedScore,
                'recommendation' => $getPerformance['outcomes'] ?? '',
                'height'    => $item->height, 
                'weight'   => $item->weight,
            ];
        });
    }

    private function getPerformance($item, $studentAge, $studentGender, $bmiBenchmarkRow) {

        $score = $item->Score;
        $skill_type_id = $item->TestTypeID;
        $test_master_id = $item->TestTypeMasterID;
        $score_criteria = $item->ScoreCriteria;
        $score_unit  = $item->ScoreUnit;
        $test_category_name = $item->TestCategoryName;

        $level = 'N.A.';
        $recommendation = '';

        if ($test_category_name === 'Body Composition' && $bmiBenchmarkRow) {
            $levels = ['UW','N','OW','OB']; 
            $level = 'OB';

            foreach ($levels as $lvl) {
                if (!empty($bmiBenchmarkRow->$lvl)) {
                    if ($item->Score < $bmiBenchmarkRow->$lvl) {
                        $level = $lvl;
                        break; 
                    }
                }
            }

            $bmiLevelLabels = [
                'UW' => "UW",
                'N'  => "Normal",
                'OW' => "OW",
                'OB' => "OB",
            ];

            return [
                'level' => $bmiLevelLabels[$level] ?? $level,
                'outcomes' => '' // You can also add BMI-specific outcomes if you have a table
            ];
        }


        $level = $this->getFitnessLevel($test_master_id, $studentAge, $studentGender, $score, $score_criteria);

        $recommendation = DB::table('fitnessRecommandation')->where('skill_reports_id', $item->TestTypeID)->where('level', '=', $level)
            ->value('recommendations');

        // Human-readable labels
        $levelLabels = [
            'L0' => "Inadequate",
            'L1' => "Very Low",
            'L2' => "Low",
            'L3' => "Developing",
            'L4' => "Moderate",
            'L5' => "Good",
            'L6' => "High",
            'L7' => "Excellent",
            'L8' => "Beyond L7",
        ];

        return [
            'level' => $level . ' (' . ($levelLabels[$level] ?? $level) . ')',
            'outcomes' => $recommendation ?? ''
        ];
    }

    /* Get Levels */
    private function getFitnessLevel($skillReportId, $studentAge, $studentGender, $score, $score_criteria)  {

        try {

            $result = DB::selectOne("SELECT get_fitness_level(:skill_report_id, :age, :gender, :score, :criteria) AS level", [
                'skill_report_id' => $skillReportId,
                'age'             => $studentAge,
                'gender'          => $studentGender,
                'score'           => $score,
                'criteria'        => $score_criteria,
            ]);

            return $result->level ?? 'N.A.';
            
        } catch (\Throwable $e) {

            \Log::error('Fitness Calculation Failed for age : '.$studentAge , [
                'age' => $studentAge,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }



    /* BMI Benchmark */
    public function getBmiBenchmark($ageGender) {
        $bmiBenchmarkRow = DB::table('LP_BMI_List')->where('Age', $ageGender)->first();
        if (!$bmiBenchmarkRow) {
            return null; 
        }
        
        return [
            'UW' => 'BMI < ' . $bmiBenchmarkRow->UW,
            'N'  => $bmiBenchmarkRow->UW . ' ≤ BMI < ' . $bmiBenchmarkRow->N,
            'OW' => $bmiBenchmarkRow->N . ' ≤ BMI < ' . $bmiBenchmarkRow->OW,
            'OB' => 'BMI ≥ ' . $bmiBenchmarkRow->OW
        ];
    }

    /* Fitness Benchmark */
    public function getBenchmark($age, $gender, $categories_id) {   

        // Fetch benchmarks from DB
        $benchmarks = DB::table('fitness_benchmarks')
            ->join('TestTypeMaster','TestTypeMaster.TestTypeId','=', 'fitness_benchmarks.skill_reports_id')
            ->join('TestCategoryMaster','TestCategoryMaster.TestCategoryID','=', 'TestTypeMaster.TestCategoryID')
            ->join('skill_reports','skill_reports.TestTypeMasterID','=', 'TestTypeMaster.TestTypeID')
            ->select('fitness_benchmarks.*', 
                'TestTypeMaster.ScoreCriteria',
                'TestTypeMaster.ScoreUnit',
                'skill_reports.skill_name'
            )
            ->where('Age', $age)
            ->where('Gender', $gender)
            ->whereIn('TestCategoryMaster.TestCategoryID', $categories_id)
            ->get();

        $formattedBenchmarks = $this->formatBenchmarkRanges($benchmarks);

        return $formattedBenchmarks;
    }

    public function formatBenchmarkRanges($benchmarks) {

        return $benchmarks->map(function($item) {
            $ranges = [];
            for ($i = 1; $i <= 7; $i++) {
                $low  = $item->{"l{$i}_low"};
                $high = $item->{"l{$i}_high"};
                if ($low !== null && $high !== null) {
                    $ranges["L{$i}"] = $this->formatValue($low, $item->ScoreUnit) . ' - ' . $this->formatValue($high, $item->ScoreUnit);
                } else {
                    $ranges["L{$i}"] = '-';
                }
            }

            return (object)[
                'skill_name'    => $item->skill_name,
                'unit'          => $item->unit,
                'ScoreCriteria' => $item->ScoreCriteria,
                'ranges'        => $ranges
            ];
        });
    }

    public function formatValue($value, $unit) {
        switch ($unit) {
            case 'msec':
                $milliseconds = $value % 1000;
                $totalSeconds = intdiv($value, 1000);
                $minutes = intdiv($totalSeconds, 60);
                $seconds = $totalSeconds % 60;

                if ($minutes > 0) {
                    $parts = [];
                    $parts[] = $minutes . 'min';
                    if ($seconds > 0) {
                        $parts[] = $seconds . 'sec';
                    }
                    if ($milliseconds > 0) {
                        $parts[] = str_pad($milliseconds, 3, '0', STR_PAD_LEFT) . 'ms';
                    }
                    return implode(' ', $parts);
                }

                $secWithMs = $value / 1000.0;
                $fmt = number_format($secWithMs, 3, '.', '');
                $trimmed = rtrim(rtrim($fmt, '0'), '.');
                return $trimmed . ' sec';

            case 'mm':
                return ($value / 10) . ' cm';

            case 'number':
                return intval($value) . ' times';
                
            case 'mL/kg/min':
                return $value . ' mL/kg/min';

            default:
                return $value;
        }
    }


    protected function getSeniorReportData($studentId, $studentAge, $studentGender, $groupedReportData) {

        $senior = [8, 9, 5, 4, 15, 3];

        $seniorData = DB::table('TestCategoryMaster')
            ->join('TestTypeMaster', 'TestTypeMaster.TestCategoryID','=', 'TestCategoryMaster.TestCategoryID')
            ->join('skill_reports', 'skill_reports.TestTypeMasterID','=', 'TestTypeMaster.TestTypeID')
            ->whereIn('TestCategoryMaster.TestCategoryID', $senior)
            ->orderByRaw('FIELD(TestCategoryMaster.TestCategoryID, ' . implode(',', $senior) . ')')
            ->select('TestCategoryMaster.TestCategoryID','TestCategoryMaster.TestCategoryName','skill_reports.skill_name')
            ->where('TestCategoryMaster.IsActive', 1)
            ->get();

        $orderedReportData = collect();
        foreach ($seniorData as $cat) {
            $categoryName = $cat->TestCategoryName . " (" . $cat->skill_name . ")";
            $data = $groupedReportData->get($cat->TestCategoryName, collect());
            $orderedReportData->put($categoryName, $data);
        }

        $categories_id2 = [3,10,12,11,8,9,5,4,15,3];
        $fitnessBenchmark = $this->getBenchmark($studentAge, $studentGender, $categories_id2);

        return [$orderedReportData, $fitnessBenchmark];
    }



    protected function getJuniorReportData_bk($studentId, $studentAge, $studentGender, $groupedReportData, $TermMasterId)  {
        
        $junior1 = [2,6,3];
        $juniorData1 = DB::table('TestCategoryMaster')
            ->join('TestTypeMaster', 'TestTypeMaster.TestCategoryID','=', 'TestCategoryMaster.TestCategoryID')
            ->join('skill_reports', 'skill_reports.TestTypeMasterID','=', 'TestTypeMaster.TestTypeID')
            ->whereIn('TestCategoryMaster.TestCategoryID', $junior1)
            ->orderByRaw('FIELD(TestCategoryMaster.TestCategoryID, ' . implode(',', $junior1) . ')')
            ->select('TestCategoryMaster.TestCategoryID','TestCategoryMaster.TestCategoryName','skill_reports.skill_name')
            ->where('TestCategoryMaster.IsActive', 1)
            ->get();

        $orderedReportData = collect();

        foreach ($juniorData1 as $cat) {
            $categoryName = $cat->TestCategoryName . " (" . $cat->skill_name . ")";
            $data = $groupedReportData->get($cat->TestCategoryName, collect());
            $orderedReportData->put($categoryName, $data);
        }

        $junior = [10,12,11];

        $allSkills = DB::table('TestCategoryMaster as tcm')
            ->join('TestTypeMaster as ttm', 'ttm.TestCategoryID', '=', 'tcm.TestCategoryID')
            ->join('skill_reports as sr', 'sr.TestTypeMasterID', '=', 'ttm.TestTypeID')
            ->whereIn('tcm.TestCategoryID', $junior)
            ->select('tcm.TestCategoryID', 'tcm.TestCategoryName', 'sr.skill_name', 'ttm.TestTypeID')
            ->get()
            ->groupBy('TestCategoryName');

        $fmsCategory = DB::table('skillreport_skilltype_termtype_mapping as sst')
            ->join('skill_reports as sr', 'sr.id', '=', 'sst.skill_report_id')
            ->leftjoin('skill_types as st', 'st.id', '=', 'sst.skill_type_id')
            ->join('TestTypeMaster as ttm', 'ttm.TestTypeID', '=', 'sr.TestTypeMasterID')
            ->join('TestCategoryMaster as tcm', 'tcm.TestCategoryID', '=', 'ttm.TestCategoryID')
            ->where('sst.student_id', $studentId)
            ->where('sst.term_master_id', $TermMasterId)
            ->select(
                'tcm.TestCategoryName',
                'sr.skill_name',
                'sr.id as skill_report_id',
                'sr.TestTypeMasterID',
                DB::raw('COUNT(sst.skill_type_id) as skill_count')
            )
            ->groupBy(
                'tcm.TestCategoryName',
                'sr.id',
                'sr.skill_name',
                'sr.TestTypeMasterID'
            )
            ->orderBy('sr.id')
            ->get()
            ->groupBy('TestCategoryName');


        $FmsReportData = collect($allSkills)->mapWithKeys(function ($skills, $categoryName) use ($fmsCategory) {
            $report = $skills->map(function ($skill) use ($fmsCategory, $categoryName) {
                $studentSkill = $fmsCategory->get($categoryName, collect())
                    ->firstWhere('skill_name', $skill->skill_name);

                $count = $studentSkill->skill_count ?? null;
                    
                    if ($count <= 1) {
                        $outcome = 'Emerging';
                    } elseif ($count == 2) {
                        $outcome = 'Developing';
                    } elseif ($count == 3) {
                        $outcome = 'Acquired';
                    } elseif ($count == 4) {
                        $outcome = 'Advanced';
                    } elseif ($count == 5) {
                        $outcome = 'Accomplished';
                    }else{
                        $outcome = 'Not Attempted';
                    }

                $recommendation = null;
                if ($studentSkill) {
                    $recommendation = DB::table('fms_recommendations')
                        ->where('skill_reports_id', $studentSkill->skill_report_id)
                        ->where('outcomes', $outcome)
                        ->value('recommendations');
                }

                return [
                    'Test_Name'      => $skill->skill_name,
                    'recommendation' => $recommendation,
                    'outcome'        => $outcome
                ];
            });

            return [$categoryName => $report->toArray()];
        });

        $categories_id1 = [6,2];
        $fitnessBenchmark = $this->getBenchmark($studentAge, $studentGender, $categories_id1);

        return [$orderedReportData, $FmsReportData, $fitnessBenchmark];
    }
    protected function getJuniorReportData(int $classId, int $studentId, int $studentAge,string $studentGender,$groupedReportData, array $termIds) {

        [$currentTermId, $previousTermId] = $termIds + [null, null];


        $juniorCategoryOrder = [2, 6, 3];
        $juniorData = DB::table('TestCategoryMaster as tcm')
            ->join('TestTypeMaster as ttm', 'ttm.TestCategoryID', '=', 'tcm.TestCategoryID')
            ->join('skill_reports as sr', 'sr.TestTypeMasterID', '=', 'ttm.TestTypeID')
            ->whereIn('tcm.TestCategoryID', $juniorCategoryOrder)
            ->where('tcm.IsActive', 1)
            ->where('ttm.TestTypeID', '!=', 1014)
            ->orderByRaw( 'FIELD(tcm.TestCategoryID, ' . implode(',', $juniorCategoryOrder) . ')' )
            ->select(
                'tcm.TestCategoryName',
                'sr.skill_name'
            )->get();

        $orderedReportData = collect();

        foreach ($juniorData as $row) {
            $key = $row->TestCategoryName . ' (' . $row->skill_name . ')';
            $orderedReportData->put(
                $key,
                $groupedReportData->get($row->TestCategoryName, collect())
            );
        }


        $fmsCategories = [10, 12, 11];

        $allSkills = DB::table('TestCategoryMaster as tcm')
            ->join('TestTypeMaster as ttm', 'ttm.TestCategoryID', '=', 'tcm.TestCategoryID')
            ->join('skill_reports as sr', 'sr.TestTypeMasterID', '=', 'ttm.TestTypeID')
            ->join('class_fitness_tests as cft', 'cft.test_type_id', '=', 'sr.TestTypeMasterID')
            ->whereIn('tcm.TestCategoryID', $fmsCategories)
            ->where('cft.class_id', $classId)
            ->where('cft.is_active', 'active')
            ->select(
                'tcm.TestCategoryName',
                'sr.skill_name'
            )
            ->get()
            ->groupBy('TestCategoryName');

        $fmsRaw = DB::table('skillreport_skilltype_termtype_mapping as sst')
            ->join('skill_reports as sr', 'sr.id', '=', 'sst.skill_report_id')
            ->join('TestTypeMaster as ttm', 'ttm.TestTypeID', '=', 'sr.TestTypeMasterID')
            ->join('TestCategoryMaster as tcm', 'tcm.TestCategoryID', '=', 'ttm.TestCategoryID')
            ->where('sst.student_id', $studentId)
            ->whereIn('sst.term_master_id', array_filter([$currentTermId, $previousTermId]))
            ->select(
                'tcm.TestCategoryName',
                'sr.skill_name',
                'sst.term_master_id',
                DB::raw('COUNT(*) as skill_count'),
                DB::raw('MAX(sr.id) as skill_report_id')
            )
            ->groupBy(
                'tcm.TestCategoryName',
                'sr.skill_name',
                'sst.term_master_id'
            )
            ->get()
            ->groupBy(['TestCategoryName', 'skill_name']);

        $FmsReportData = $allSkills->mapWithKeys(function ($skills, $categoryName) use ( $fmsRaw, $currentTermId, $previousTermId) {

            $records = $skills->map(function ($skill) use ($fmsRaw,$categoryName, $currentTermId,$previousTermId ) {

                $rows = $fmsRaw[$categoryName][$skill->skill_name] ?? collect();

                $current = $rows->firstWhere('term_master_id', $currentTermId);
                $previous = $rows->firstWhere('term_master_id', $previousTermId);

                $currentCount  = $current->skill_count ?? 0;
                $previousCount = $previous->skill_count ?? 0;

                $outcome = match (true) {
                    $currentCount === 1 => 'Emerging',
                    $currentCount === 2 => 'Developing',
                    $currentCount === 3 => 'Acquired',
                    $currentCount === 4 => 'Advanced',
                    $currentCount >= 5  => 'Accomplished',
                    default             => 'N.A.',
                };

                $recommendation = null;
                if ($current?->skill_report_id) {
                    $recommendation = DB::table('fms_recommendations')
                        ->where('skill_reports_id', $current->skill_report_id)
                        ->where('outcomes', $outcome)
                        ->value('recommendations');
                }

                return [
                    'Test_Name'      => $skill->skill_name,
                    'current_count'  => $currentCount,
                    'previous_count' => $previousCount,
                    'outcome'        => $outcome,
                    'recommendation' => $recommendation,
                ];
            });

            return [$categoryName => $records->values()];
        });


        $fitnessBenchmark = $this->getBenchmark( $studentAge,  $studentGender, [6, 2] );
        return [
            $orderedReportData,
            $FmsReportData,
            $fitnessBenchmark
        ];
    }

    /*
    * CWSN Report data: 05-09-2026
    */

    protected function getCWSNReportData($studentId, $pwd_cat_id, $studentAge, $studentGender, $groupedReportData) {
        $cwsnCat = [23, 40, 39, 3];

        $defaultTests = [
            23 => '20-m PACER',
            40 => 'Curl-up',
            39 => 'Modified Thomas Test',
            3  => 'BMI',
        ];

        $cwsnData = DB::table('TestCategoryMaster')
            ->join('TestTypeMaster', 'TestTypeMaster.TestCategoryID', '=', 'TestCategoryMaster.TestCategoryID')
            ->join('skill_reports', 'skill_reports.TestTypeMasterID','=', 'TestTypeMaster.TestTypeID')
            ->whereIn('TestCategoryMaster.TestCategoryID', $cwsnCat)
            ->where('TestCategoryMaster.IsActive', 1)
            ->select('TestCategoryMaster.TestCategoryID','TestCategoryMaster.TestCategoryName','skill_reports.skill_name')
            ->orderByRaw('FIELD(TestCategoryMaster.TestCategoryID, ' . implode(',', $cwsnCat) . ')')
            ->get();

        $orderedReportData = collect();

        foreach ($cwsnCat as $categoryId) {
            $categoryTests = $cwsnData->where('TestCategoryID',$categoryId);
            if ($categoryTests->isEmpty()) {
                continue;
            }

            $categoryName = $categoryTests->first()->TestCategoryName;

            $studentTest = null;

            foreach ($categoryTests as $test) {
                $currentResult = $groupedReportData
                    ->get($categoryName, collect())
                    ->get('Current_Term', collect())
                    ->first(function ($row) use ($test) {
                        return trim($row['Test_Name'] ?? '') ===
                            trim($test->skill_name);
                    });

                if ($currentResult) {
                    $studentTest = $test;
                    break;
                }
            }

            if (!$studentTest) {
                foreach ($categoryTests as $test) {
                    $previousResult = $groupedReportData
                        ->get($categoryName, collect())
                        ->get('Previous_Term', collect())
                        ->first(function ($row) use ($test) {
                            return trim($row['Test_Name'] ?? '') ===
                                trim($test->skill_name);
                        });

                    if ($previousResult) {
                        $studentTest = $test;
                        break;
                    }
                }
            }

            if (!$studentTest) {
                $defaultTestName = $defaultTests[$categoryId] ?? null;

                $studentTest = $categoryTests->first(function ($test) use ($defaultTestName) {
                    return trim($test->skill_name) ===
                        trim($defaultTestName);
                });

                if (!$studentTest) {
                    $studentTest = $categoryTests->first();
                }
            }

            $data = $groupedReportData->get(
                $categoryName,
                collect()
            );

            $currentTerm = $data
                ->get('Current_Term', collect())
                ->first(function ($row) use ($studentTest) {
                    return trim($row['Test_Name'] ?? '') ===
                        trim($studentTest->skill_name);
                });

            $previousTerm = $data
                ->get('Previous_Term', collect())
                ->first(function ($row) use ($studentTest) {
                    return trim($row['Test_Name'] ?? '') ===
                        trim($studentTest->skill_name);
                });

            if (!$currentTerm) {
                $currentTerm = [
                    'Category'       => $categoryName,
                    'TermId'         => null,
                    'created_at'     => null,
                    'ni'             => '',
                    'afz'            => '',
                    'hfz'            => '',
                    'Test_Name'      => $studentTest->skill_name,
                    'score'          => '',
                    'recommendation' => '',
                    'height'         => '',
                    'weight'         => '',
                ];
            }

            if (!$previousTerm) {
                $previousTerm = [
                    'Category'       => $categoryName,
                    'TermId'         => null,
                    'created_at'     => null,
                    'ni'             => '',
                    'afz'            => '',
                    'hfz'            => '',
                    'Test_Name'      => $studentTest->skill_name,
                    'score'          => '',
                    'recommendation' => '',
                    'height'         => '',
                    'weight'         => '',
                ];
            }

            $displayKey = $categoryName . ' (' . $studentTest->skill_name . ')';

            $orderedReportData->put(
                $displayKey,
                collect([
                    'Current_Term'  => collect([$currentTerm]),
                    'Previous_Term' => collect([$previousTerm]),
                ])
            );
        }

        return $orderedReportData;
    }


    public function mapCWSNReportData($reportData, $studentAge, $studentGender, $ageGender){

        return $reportData->map(function ($item) use ($studentAge, $studentGender, $ageGender) {

            $normalizedScore = $this->formatValue($item->Score, $item->ScoreUnit);

           // echo "string"; print_r(expression); exit();

            $response = [
                'Category'       => $item->TestCategoryName,
                'TermId'         => $item->TermId,
                'created_at'     => \Carbon\Carbon::parse($item->created_at)->format('d M Y'),
                'very_lean'      => '',
                'ni'             => '',
                'afz'            => '',
                'hfz'            => '',
                'health_risk'    => '',
                'bmiLevel'       => '',
                'Test_Name'      => $item->skill_name,
                'score'          => $normalizedScore,
                'recommendation' => '',
                'height'         => $item->height,
                'weight'         => $item->weight,
            ];


            if($item->TestTypeID == 32){

                switch ((int)$item->Score) {
                    case 1000:
                    case 1001:
                    case 1002:
                        $formatScore = 'Not calculated';
                        break;
                    
                    default:
                        $formatScore  = $this->formatValue($item->Score, $item->ScoreUnit);
                        break;
                }
                

                $getPerformance = $this->getHealthyZone($item, $studentAge, $studentGender);
                $response['score'] =  $formatScore;
                $response['ni']  = $getPerformance['ni'];
                $response['afz'] = $getPerformance['afz'];
                $response['hfz'] = $getPerformance['hfz'];
                $response['recommendation'] = $getPerformance['outcomes'] ?? '';

                return $response;
                
            }
            

            if ($item->TestTypeID == 33) {

                $formatScore  = $this->formatValue($item->Score, $item->ScoreUnit);
                $getPerformance = $this->getHealthyZone($item, $studentAge, $studentGender);

                $response['score'] =  $formatScore;
                $response['very_lean'] = $getPerformance['very_lean'];
                $response['ni']  = $getPerformance['ni'];
                $response['hfz'] = $getPerformance['hfz'];
                $response['health_risk'] = $getPerformance['health_risk'];
                $response['bmiLevel'] = $getPerformance['bmiLevel'];
                $response['recommendation'] = $getPerformance['outcomes'] ?? '';

                return $response;
            }

           if ($item->TestCategoryID == 39) {
                $leftScore = $this->formatValue($item->LeftScore, $item->ScoreUnit);
                $RightScore = $this->formatValue($item->RightScore, $item->ScoreUnit);
                $normalizedScore = 'Left-' . $leftScore . ' | Right-' . $RightScore;
                $getPerformance = $this->getHealthyZone($item, $studentAge, $studentGender);

                $response['score'] =  $normalizedScore;
                $response['ni']  = $getPerformance['ni'];
                $response['afz'] = $getPerformance['afz'];
                $response['hfz'] = $getPerformance['hfz'];
                $response['recommendation'] = $getPerformance['outcomes'] ?? '';

                return $response;
            }
            // Pass/Fail Tests
            if (in_array($item->TestTypeID, [56, 58])) {

                $getPerformance = $this->getHealthyZone($item, $studentAge, $studentGender);

                $response['score'] = $item->Score == '1' ? 'Pass' : 'Fail';
                $response['ni'] = $getPerformance['ni'] == '1' ? 'Pass' : 'Fail';
                $response['afz'] = $getPerformance['afz'];
                $response['hfz'] = $getPerformance['hfz'] == '1' ? 'Pass' : 'Fail';
                $response['recommendation'] = $getPerformance['outcomes'] ?? '';

                return $response;
            }
            // Other Tests
            $getPerformance = $this->getHealthyZone($item, $studentAge, $studentGender);

            $response['ni'] = $getPerformance['ni'];
            $response['afz'] = $getPerformance['afz'];
            $response['hfz'] = $getPerformance['hfz'];
            $response['recommendation'] = $getPerformance['outcomes'] ?? '';

            return $response;
        });
    }

    private function getHealthyZone($item, $studentAge, $studentGender){

        $testTypeId = $item->TestTypeMasterID;

        $benchmark = DB::table('cwsn_fitness_benchmarks')
            ->select([
                'very_lean',
                'ni',
                'afz',
                'hfz',
                'health_risk',
                'score_criteria',
            ])
            ->where('test_type_id', $testTypeId)
            ->where('age', $studentAge)
            ->where('gender', $studentGender)
            ->first();

        if (!$benchmark) {
            return [
                'level'        => null,
                'outcomes'     => 'Benchmark not available',
                'very_lean'    => null,
                'ni'           => null,
                'afz'          => null,
                'hfz'          => null,
                'health_risk'  => null,
                'bmiLevel'     => null,
            ];
        }

        $score = $item->Score ?? $item->RightScore ?? null;

        $scoreDivisors = [
            1031 => 1000,
            1025 => 10,
            1033 => 10,
        ];

        if ($score !== null && isset($scoreDivisors[$testTypeId])) {
            $score /= $scoreDivisors[$testTypeId];
        }


        if ($benchmark->ni !== null && $score <= $benchmark->ni) {
            $recommendParam = 'ni';
        } elseif ($benchmark->hfz !== null && $score >= $benchmark->hfz) {
            $recommendParam = 'hfz';
        } else {
            $recommendParam = 'afz';
        }

        $bmiLevel = null;
        if ($item->TestTypeID == 33) {
            $score = (float) $item->Score;
            $veryLean   = $benchmark->very_lean !== null
                ? (float) $benchmark->very_lean
                : null;

            $healthRisk = $benchmark->health_risk !== null
                ? (float) $benchmark->health_risk
                : null;

            if ($veryLean !== null && $score <= $veryLean) {
                $bmiLevel = 'Very Lean';
                $recommendParam = 'very_lean';                
            }elseif ($healthRisk !== null && $score >= $healthRisk) {
                $bmiLevel = 'Health Risk';
                $recommendParam = 'health_risk';
            }elseif ($this->isScoreInRange($score, $benchmark->ni)) {
                $bmiLevel = 'Needs Improvement';
                $recommendParam = 'ni';
            }elseif ($this->isScoreInRange($score, $benchmark->hfz)) {
                $bmiLevel = 'Healthy Fitness Zone';
                $recommendParam = 'hfz';
            }
        }

        $recommendation = DB::table('cwsn_recommendations')
            ->where('skill_reports_id', $item->TestTypeID)
            ->where('outcomes', $recommendParam)
            ->value('recommendations');

        if($item->TestTypeID == 32 && in_array($score, ['1000', '1001', '1002'])){
            switch ($score) {

                case '1000':
                    $recommendation = 'VO₂ max (aerobic capacity) not calculated as time exceeded 13 minutes.';
                    break;

                case '1001':
                $recommendation = 'VO₂ max (aerobic capacity) not calculated as BMI is unavailable.';
                    break;

                case '1002':
                    $recommendation = "VO₂ max (aerobic capacity) not calculated as age is outside the valid range.";
                    break;
            }
        }

        $ni  = $benchmark->ni;
        $hfz = $benchmark->hfz;

        switch ($benchmark->score_criteria) {

            case 'More_is_better':
                $ni  = $ni !== null ? '≤' . $ni : null;
                $hfz = $hfz !== null ? '≥' . $hfz : null;
                break;

            case 'Less_is_better':
                $ni  = $ni !== null ? '≥' . $ni : null;
                $hfz = $hfz !== null ? '≤' . $hfz : null;
                break;
        }

        return [
            'very_lean'   => $benchmark->very_lean !== null ? '≤' . $benchmark->very_lean : null,
            'ni'          => $ni,
            'afz'         => $benchmark->afz,
            'hfz'         => $hfz,
            'health_risk' => $benchmark->health_risk !== null ? '≥' . $benchmark->health_risk : null,
            'outcomes'    => $recommendation,
            'bmiLevel'    => $bmiLevel,
        ];
    }
    
    private function isScoreInRange($score, $range){
        
        if ($range === null || $range === '') {
            return false;
        }

        $range = str_replace(' ', '', $range);

        if (strpos($range, '-') !== false) {

            [$min, $max] = explode('-', $range, 2);

            return $score >= (float) $min
                && $score <= (float) $max;
        }

        return $score == (float) $range;
    }

    public function getCWSNBmiBenchmark($studentAge, $studentGender) {
        $bmiBenchmarkRow = DB::table('cwsn_fitness_benchmarks')
        ->where('test_type_id', 1040)
        ->where('age', $studentAge)
        ->where('gender', $studentGender)
        ->first();


        if (!$bmiBenchmarkRow) {
            return null; 
        }
        
        return [
            'very_lean' => ' ≤ ' . $bmiBenchmarkRow->very_lean,
            'hfz' => $bmiBenchmarkRow->hfz,
            'ni' => $bmiBenchmarkRow->ni,
            'health_risk' => ' ≥ ' . $bmiBenchmarkRow->health_risk,
        ];
    }
}
