<?php

namespace App\Services;

use App\Models\Sstudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FitnessService
{
    /**
     * Get fitness level for a student
     */
    public function getFitnessTestLevel($studentId, $testTypeMasterId, $score, $scoreCriteria)
    {
        $student = Sstudent::where('id', $studentId)
            ->select('gender', 'dob', 'id')
            ->first();

        if (!$student) {
            Log::warning("Student not found", ['student_id' => $studentId]);
            return null;
        }

        $dob = Carbon::parse($student->dob);
        $studentAge = $dob->age;
        $gender = strtolower($student->gender) === 'male' ? 'Boys' : 'Girls';

        // return($studentAge.$gender.$score.$scoreCriteria.$testTypeMasterId);


        try {
            $result = DB::selectOne("
                SELECT get_fitness_level(:skill_report_id, :age, :gender, :score, :criteria) AS level
            ", [
                'skill_report_id' => $testTypeMasterId,
                'age'             => $studentAge,
                'gender'          => $gender,
                'score'           => $score,
                'criteria'        => $scoreCriteria,
            ]);

            return $result->level ?? 'N.A.';

        } catch (\Throwable $e) {
            Log::error('Fitness Level Calculation Failed', [
                'student_id' => $student->id,
                'age'        => $studentAge,
                'exception'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getBMILevel($studentId, $score)
	{

        $student = Sstudent::where('id', $studentId)
            ->select('gender', 'dob', 'id')
            ->first();

        if (!$student) {
            Log::warning("Student not found", ['student_id' => $studentId]);
            return null;
        }
        $dob = Carbon::parse($student->dob);
        $studentAge = $dob->age;
        $studentGender = $student->gender;
        $ageGender = $studentAge . strtolower(substr($studentGender, 0, 1));

        $result = DB::selectOne("CALL GetBmiLevel(?, ?)", [$ageGender, $score]);
        return $result->level_code;

    }
}
