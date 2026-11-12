<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\FitnessService;

class UpdateFitnessLevels extends Command
{
    protected $signature = 'fitness:update-levels';
    protected $description = 'Update fitness test levels where level is null';
    protected $fitnessService;

    public function __construct(FitnessService $fitnessService)
    {
        parent::__construct();
        $this->fitnessService = $fitnessService;
    }

    public function handle()
    {
        $this->info("Starting update of fitness levels...");

        $updatedCount = 0;

        DB::table('SeniorTestResults as r')
            ->join('skill_reports as sr', 'sr.id', '=', 'r.TestTypeID')
            // ->whereNull('level')
		 ->where('level','N.A.')
        //  ->where('SchoolID', '1786')
            ->orderBy('r.ResultId')
            ->select(
                'r.*',
                'sr.TestTypeMasterID'
            )
            ->chunk(1000, function ($results) use (&$updatedCount) {

                $testTypeMasterIds = $results
                    ->pluck('TestTypeMasterID')
                    ->unique()
                    ->toArray();

                $criteriaMap = DB::table('TestTypeMaster')
                    ->whereIn('TestTypeID', $testTypeMasterIds)
                    ->pluck('ScoreCriteria', 'TestTypeID')
                    ->toArray();

                foreach ($results as $row) {
                    $studentId = $row->StudentID;
                    $testTypeId = $row->TestTypeID;
                    $testTypeMasterId = $row->TestTypeMasterID;
                    $score = $row->Score;

                    $criteria = $criteriaMap[$testTypeMasterId] ?? null;

                    if ($testTypeId == '18') {
                        $level = $this->fitnessService->getBMILevel(
                            $studentId,
                            $score
                        );
                    } else {
                        $level = $this->fitnessService->getFitnessTestLevel(
                            $studentId,
                            $testTypeMasterId,
                            $score,
                            $criteria
                        );
                    }

                    DB::table('SeniorTestResults')
                        ->where('ResultId', $row->ResultId)
                        ->update(['level' => $level]);
                        
                    // $this->info("Update student: {$studentId} and level: {$level}");

                    $updatedCount++;
                }
            });

        $this->info("[" . now()->format('Y-m-d H:i:s') . "] Update completed. Total records updated: {$updatedCount}");
        
    }
}
