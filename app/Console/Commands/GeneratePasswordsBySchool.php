<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateStudentPasswordJob;

class GeneratePasswordsBySchool extends Command
{
    protected $signature = 'students:generate-passwords
        {--chunk=500 : Number of students per batch}';

    protected $description = 'Generate hashed passwords for students school-wise in a safe and efficient way';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        // Fetch schools with pending password jobs
        $schoolIds = DB::table('school_password_jobs')
            ->where('status', 'pending')
            ->limit(10) // process 10 schools per run to avoid overload
            ->pluck('school_id')
            ->toArray();

        if (empty($schoolIds)) {
            $this->info('No pending schools found');
            return Command::SUCCESS;
        }

        $this->info('Schools to process: ' . implode(', ', $schoolIds));

        foreach ($schoolIds as $schoolId) {
            $this->processSchool((int)$schoolId, $chunk);
        }

        return Command::SUCCESS;
    }

    /**
     * Lock school, dispatch jobs in chunks, mark school as done
     */
    private function processSchool(int $schoolId, int $chunk): void
    {
        // Attempt to lock school
        $locked = DB::table('school_password_jobs')
            ->where('school_id', $schoolId)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if ($locked === 0) {
            return; // already picked by another process
        }

        try {
            $query = DB::table('students')
                ->where('school_id', $schoolId)
                ->where('password_generated', 0)
                ->orderBy('id');

            $total = $query->count();

            if ($total === 0) {
                $this->info("School {$schoolId}: nothing to process");

                DB::table('school_password_jobs')
                    ->where('school_id', $schoolId)
                    ->update(['status' => 'done']);

                return;
            }

            $this->info("School {$schoolId}: {$total} students");

            // Chunk through students and dispatch job for each
            $query->chunkById($chunk, function ($students) {
                foreach ($students as $student) {
                    GenerateStudentPasswordJob::dispatch($student->id);
                        // ->onQueue('report_generation'); // use dedicated queue
                }
            });

            DB::table('school_password_jobs')
                ->where('school_id', $schoolId)
                ->update(['status' => 'done']);

            $this->info("School {$schoolId}: jobs dispatched");

        } catch (\Throwable $e) {
            DB::table('school_password_jobs')
                ->where('school_id', $schoolId)
                ->update(['status' => 'failed']);

            throw $e;
        }
    }
}
