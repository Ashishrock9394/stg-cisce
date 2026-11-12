<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlattenFitnessDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fitness:flatten-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flatten senior fitness data into flattened_fitness_data table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting flatten fitness data...');

        try {

            DB::table('flattened_fitness_data')->truncate();

            DB::statement("
                INSERT INTO flattened_fitness_data (
                    region,
                    zonename,
                    state,
                    district,
                    test_type_id,
                    skill_name,
                    level,
                    total_count,
                    created_at,
                    updated_at
                )
                SELECT
                    s.region,
                    s.zonename,
                    s.state,
                    s.district,
                    r.TestTypeID,
                    sr.skill_name,
                    r.level,
                    COUNT(*),
                    NOW(),
                    NOW()
                FROM SeniorTestResults r
                INNER JOIN schools s ON s.id = r.SchoolID
                INNER JOIN skill_reports sr ON sr.id = r.TestTypeID
                GROUP BY
                    s.region,
                    s.zonename,
                    s.state,
                    s.district,
                    r.TestTypeID,
                    sr.skill_name,
                    r.level
            ");

            $count = DB::table('flattened_fitness_data')->count();

            $this->info("[" . now()->format('Y-m-d H:i:s') . "] Successfully inserted {$count} records.");

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}