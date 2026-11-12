<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSchoolsAssessment extends Command
{
    protected $signature = 'schools:update-assessment';

    protected $description = 'Insert or update schools assessment summary by school_id';

    public function handle()
    {        
        $this->info('[' . now()->toDateTimeString() . '] Schools Assessment scheduler start.');

        DB::statement("CREATE TEMPORARY TABLE temp_student_metrics AS
            SELECT 
                s.id as student_id,
                s.school_id,
                s.is_pwd,
                s.paymentStatus,
                COALESCE(f.fms_count, 0) as fms_count,
                COALESCE(se.senior_count, 0) as senior_count
            FROM students s
            LEFT JOIN (
                SELECT student_id, COUNT(DISTINCT skill_report_id) AS fms_count
                FROM skillreport_skilltype_termtype_mapping
                GROUP BY student_id
            ) f ON f.student_id = s.id
            LEFT JOIN (
                SELECT StudentID, COUNT(DISTINCT TestTypeID) AS senior_count
                FROM SeniorTestResults
                GROUP BY StudentID
            ) se ON se.StudentID = s.id
            WHERE s.status = 'active' AND s.academic_year = '2026-2027'");

        DB::statement("ALTER TABLE temp_student_metrics ADD INDEX (school_id)");

        DB::table('schools')
            ->select('id', 'school_code', 'school_name', 'region', 'zonename', 'state', 'district')
            ->chunkById(100, function ($schools) {
                $schoolIds = $schools->pluck('id')->toArray();
                $idsString = implode(',', $schoolIds);

            try {
                DB::statement("
                    INSERT INTO schools_assessment (
                        school_id, school_code, school_name, region, zonename, state, district,
                        registered_students, completed, ongoing, yet_to_start, cwsn_count, pymt_count, academic_year, updated_at
                    )
                    SELECT 
                        sc.id, sc.school_code, sc.school_name, sc.region, sc.zonename, sc.state, sc.district,
                        COUNT(tm.student_id) as registered_students,
                        SUM(CASE 
                            WHEN (tm.fms_count = 15 AND tm.senior_count = 3) OR (tm.senior_count = 6) 
                            THEN 1 ELSE 0 
                        END) as completed,
                        SUM(CASE 
                            WHEN (tm.fms_count > 0 OR tm.senior_count > 0) 
                            AND NOT ((tm.fms_count = 15 AND tm.senior_count = 3) OR (tm.senior_count = 6))
                            THEN 1 ELSE 0 
                        END) as ongoing,
                        SUM(CASE WHEN tm.fms_count = 0 AND tm.senior_count = 0 THEN 1 ELSE 0 END) as yet_to_start,
                        SUM(CASE WHEN tm.is_pwd = '1' THEN 1 ELSE 0 END) as cwsn_count,
                        SUM(CASE WHEN tm.paymentStatus = 'paid' THEN 1 ELSE 0 END) as pymt_count,
                        '2026-2027',
                        NOW()
                    FROM schools sc
                    LEFT JOIN temp_student_metrics tm ON sc.id = tm.school_id
                    WHERE sc.id IN ($idsString)
                    GROUP BY sc.id, sc.school_code, sc.school_name, sc.region, sc.zonename, sc.state, sc.district
                    ON DUPLICATE KEY UPDATE
                        school_code = VALUES(school_code),
                        school_name = VALUES(school_name),
                        region = VALUES(region),
                        zonename = VALUES(zonename),
                        state = VALUES(state),
                        district = VALUES(district),
                        registered_students = VALUES(registered_students),
                        completed = VALUES(completed),
                        ongoing = VALUES(ongoing),
                        yet_to_start = VALUES(yet_to_start),
                        academic_year = VALUES(academic_year),
                        cwsn_count = VALUES(cwsn_count),
                        pymt_count = VALUES(pymt_count),
                        updated_at = NOW()
                ");

            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's the integrity constraint violation error
                if ($e->getCode() === '23000') {
                    $this->error("Error detected in current chunk. Scanning for schools with missing zonename...");
                    
                    $badSchools = $schools->whereNull('zonename');
                    
                    foreach ($badSchools as $school) {
                        $this->warn(sprintf(
                            "CRITICAL: School ID %d (%s) is missing a 'zonename'.",
                            $school->id,
                            $school->school_name
                        ));
                    }
                }
                
                throw $e;
            }

        });

        DB::statement("DROP TEMPORARY TABLE temp_student_metrics");
        
        $this->info('[' . now()->toDateTimeString() . '] Schools assessment inserted/updated successfully.');
    }
}