<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLowerFitnessSummry extends Command
{
    protected $signature = 'sync:lower-fitness-summary';

    protected $description = 'Synchronize SeniorTestResults into LowerTestResultsSummary table';

    public function handle()
    {
        DB::statement("
            INSERT INTO LowerTestResultsSummary ( 
                student_id,
                term_id,
                school_id,
                flamingo_balance,
                plate_tapping,
                bmi,
                height,
                weight,
                created_at,
                updated_at
            )
            SELECT
                r.StudentID,
                r.TermId,
                r.SchoolID,

                -- Flamingo-Balance (TestTypeID = 17)
                MAX(CASE 
                    WHEN r.TestTypeID = 17
                    THEN r.Score 
                END),
                -- plate_tapping (TestTypeID = 16)
                MAX(CASE 
                    WHEN r.TestTypeID = 16
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
            WHERE r.created_at >= '2026-07-01' AND s.class_id BETWEEN 1 AND 3

            GROUP BY r.SchoolID, r.StudentID, r.TermId

            ON DUPLICATE KEY UPDATE
                flamingo_balance = VALUES(flamingo_balance),                
                plate_tapping = VALUES(plate_tapping),
                bmi          = VALUES(bmi),
                height       = VALUES(height),
                weight       = VALUES(weight),
                updated_at   = NOW()
        ");

        $this->info('LowerTestResultsSummary synchronized successfully ');
    }
}
