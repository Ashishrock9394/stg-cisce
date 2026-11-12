<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateTermMaster extends Command
{
    protected $signature = 'update:term-master';
    protected $description = 'Auto generate Academic year and a single Full-Term when current year is about to expire';

    public function handle()
    {
        $schoolIds = DB::table('schools')->where('status', 1)->pluck('id');

        $this->info("Checking schools for upcoming academic year expiration...");

        foreach ($schoolIds as $schoolId) {
            $latestYear = DB::table('term_masters')
                ->where('school_id', $schoolId)
                ->orderBy('academic_year_end', 'desc')
                // ->value('school_id');
                ->first();

            if (!$latestYear) {
                $this->info("No existing term for school ID {$schoolId}, creating first academic year...");
                $currentYear = now()->year;
                $newYearStart = Carbon::createFromDate($currentYear, 4, 1)->startOfDay();
                $newYearEnd = $newYearStart->copy()->addYear()->subDay()->endOfDay();

                $newAcademicYear = $newYearStart->format('Y') . '-' . $newYearEnd->format('Y');

                DB::table('term_masters')->insert([
                    'school_id' => $schoolId,
                    'term_name' => 'Full-Term',
                    'academic_year' => $newAcademicYear,
                    'academic_year_start' => $newYearStart,
                    'academic_year_end' => $newYearEnd,
                    'term_start_date' => $newYearStart,
                    'term_end_date' => $newYearEnd,
                    'is_active' => 1,
                    'camp_type' => 1,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info("Created first academic year {$newAcademicYear} for school ID {$schoolId}");

                continue;
            }

            $today = Carbon::today();
            $endDate = Carbon::parse($latestYear->academic_year_end);

            if ($today->diffInDays($endDate, false) <= 10) {
                $this->info("Academic year for school ID {$schoolId} is about to expire. Creating new Full-Term...");

                $currentYearEnd = Carbon::parse($latestYear->academic_year_end);

                $newYearStart = $currentYearEnd->copy()->addDay()->startOfDay();
                $newYearEnd = $newYearStart->copy()->addYear()->subDay()->endOfDay();

                $newAcademicYear = $newYearStart->format('Y') . '-' . $newYearEnd->format('Y');

                DB::table('term_masters')->insert([
                    'school_id' => $schoolId,
                    'term_name' => 'Full-Term',
                    'academic_year' => $newAcademicYear,
                    'academic_year_start' => $newYearStart,
                    'academic_year_end' => $newYearEnd,
                    'term_start_date' => $newYearStart,
                    'term_end_date' => $newYearEnd,
                    'is_active' => 1,
                    'camp_type' => 1,
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info("New academic year {$newAcademicYear} with Full-Term created for school ID {$schoolId}.");
            } else {
                $this->info("School ID {$schoolId} still has time before current academic year ends.");
            }
        }

        $this->info("Term master update completed.");
    }
}