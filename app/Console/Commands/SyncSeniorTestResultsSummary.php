<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSeniorTestResultsSummary extends Command
{
    protected $signature = 'sync:senior-test-summary';

    protected $description = 'Synchronize SeniorTestResults into SeniorTestResultsSummary table';

    public function handle()
    {
        DB::statement("
            INSERT INTO SeniorTestResultsSummary ( 
                school_id,
                student_id,
                term_id,
                sit_and_reach,
                run_600m,
                pushups,
                dash_50m,
                curlup,
                bmi,
                height,
                weight,
                created_at,
                updated_at
            )
            SELECT
                r.SchoolID,
                r.StudentID,
                r.TermId,

                -- Sit & Reach (TestTypeID = 22)
                MAX(CASE 
                    WHEN r.TestTypeID = 22
                    THEN r.Score 
                END),

                -- Run 600m (TestTypeID = 20)
                MAX(CASE 
                    WHEN r.TestTypeID = 20 
                    THEN r.Score 
                END),

                -- Pushups (TestTypeID = 23)
                MAX(CASE 
                    WHEN r.TestTypeID = 23 
                    THEN r.Score 
                END),

                -- Dash 50m (TestTypeID = 19)
                MAX(CASE 
                    WHEN r.TestTypeID = 19 
                    THEN r.Score 
                END),

                -- Curlup (TestTypeID = 21)
                MAX(CASE 
                    WHEN r.TestTypeID = 21 
                    THEN r.Score 
                END),

                -- BMI (derived)
                MAX(CASE 
                    WHEN r.TestTypeID = 18
                    THEN r.Score 
                END),

                -- Height
                MAX(CASE 
                    WHEN r.height IS NOT NULL 
                    THEN CONCAT(r.height, ' cm') 
                END),

                -- Weight
                MAX(CASE 
                    WHEN r.weight IS NOT NULL 
                    THEN CONCAT(r.weight, ' kg') 
                END),

                NOW(),
                NOW()

            FROM SeniorTestResults r

            INNER JOIN students s ON s.id = r.StudentID
            WHERE r.created_at >= '2026-07-01' AND s.class_id BETWEEN 4 AND 12
            
            GROUP BY r.SchoolID, r.StudentID, r.TermId

            ON DUPLICATE KEY UPDATE
                sit_and_reach = VALUES(sit_and_reach),
                run_600m     = VALUES(run_600m),
                pushups      = VALUES(pushups),
                dash_50m     = VALUES(dash_50m),
                curlup       = VALUES(curlup),
                bmi          = VALUES(bmi),
                height       = VALUES(height),
                weight       = VALUES(weight),
                updated_at   = NOW()
        ");

        $this->info('SeniorTestResultsSummary synchronized successfully.');
    }
}
