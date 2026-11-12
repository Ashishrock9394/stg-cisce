<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Sstudent;
use App\Models\ScustomClass;

class PromoteStudentsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $schoolId;

    public $timeout = 3600;
    public $tries = 3;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function uniqueId()
    {
        return $this->schoolId;
    }

    public function handle()
    {
        try {
            \Log::info('JOB STARTED', ['school_id' => $this->schoolId]);
            $year = date('Y');
            $month = date('m');

            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;

                [$startYear, $endYear] = explode('-', $academicYear);
                $previousAcademicYear = ($startYear - 1) . '-' . ($endYear - 1);

            $cacheKey = 'classes_ordered_' . $this->schoolId;

            $classes = cache()->remember($cacheKey, 3600, function () {
                return DB::table('class')
                    ->where('status', 1)
                    ->orderBy('orders')
                    ->get();
            });

            $nextClassMap = [];
            foreach ($classes as $i => $class) {
                $nextClassMap[$class->id] = $classes[$i + 1]->id ?? null;
            }

            $transferClassId = 12;

            $customClassIds = DB::table('class_promotion_status')
                ->where('school_id', $this->schoolId)
                ->where('status', 'pending')
                ->pluck('custom_class_id')
                ->toArray();

            collect($customClassIds)->chunk(5)->each(function ($classChunk) use (
                $academicYear,
                $nextClassMap,
                $previousAcademicYear,
                $transferClassId
            ) {
                DB::transaction(function () use ($classChunk, $academicYear, $nextClassMap, $previousAcademicYear, $transferClassId) {

                    $chunkIds = $classChunk->toArray();

                    DB::table('class_promotion_status')
                        ->where('school_id', $this->schoolId)
                        ->whereIn('custom_class_id', $chunkIds)
                        ->update([
                            'status' => 'processing',
                            'started_at' => now(),
                            'updated_at' => now()
                        ]);

                    $promotedCount = [];
                    $transferredCount = [];
                    $customClasses = [];

                    Sstudent::whereIn('custom_class_id', $chunkIds)
                        ->chunkById(500, function ($students) use (
                            &$promotedCount,
                            &$transferredCount,
                            &$customClasses,
                            $academicYear,
                            $nextClassMap,
                            $previousAcademicYear,
                            $transferClassId
                        ) {
                            foreach ($students as $student) {

                                if ($student->academic_year !== $previousAcademicYear) {
                                    continue;
                                }
                                
                                $sourceCustomClassId = $student->custom_class_id;

                                $promotedCount[$sourceCustomClassId] = $promotedCount[$sourceCustomClassId] ?? 0;
                                $transferredCount[$sourceCustomClassId] = $transferredCount[$sourceCustomClassId] ?? 0;

                                if ($student->class_id == $transferClassId) {
                                    $student->update([
                                        'status' => 'transfer',
                                    ]);

                                    $transferredCount[$sourceCustomClassId]++;
                                    continue;
                                }

                                $nextClassId = $nextClassMap[$student->class_id] ?? null;
                                if (!$nextClassId) {
                                    continue;
                                }

                                $key = $nextClassId . '_' . $student->section_id;

                                if (!isset($customClasses[$key])) {
                                    $customClass = ScustomClass::where([
                                        'school_id' => $this->schoolId,
                                        'class_id' => $nextClassId,
                                        'section' => $student->section_id,
                                    ])
                                    ->orderBy('id')
                                    ->lockForUpdate()
                                    ->first();

                                    if (!$customClass) {
                                        try {
                                            $customClass = ScustomClass::create([
                                                'school_id' => $this->schoolId,
                                                'class_id' => $nextClassId,
                                                'section' => $student->section_id,
                                                'nomenclature' => 'Class ' . $nextClassId,
                                                'orders' => (ScustomClass::where('school_id', $this->schoolId)->max('orders') ?? 0) + 1,
                                                'status' => 1,
                                            ]);
                                        } catch (\Exception $e) {
                                            $customClass = ScustomClass::where([
                                                'school_id' => $this->schoolId,
                                                'class_id' => $nextClassId,
                                                'section' => $student->section_id,
                                            ])
                                            ->orderBy('id')
                                            ->first();
                                        }
                                    }

                                    $customClasses[$key] = $customClass;
                                }

                                $student->update([
                                    'class_id' => $nextClassId,
                                    'custom_class_id' => $customClasses[$key]->id,
                                    'academic_year' => $academicYear
                                ]);

                                $promotedCount[$sourceCustomClassId]++;
                            }
                        }, 'id');

                    foreach ($chunkIds as $customClassId) {
                        DB::table('class_promotion_status')
                            ->where('school_id', $this->schoolId)
                            ->where('custom_class_id', $customClassId)
                            ->update([
                                'promoted_students' => $promotedCount[$customClassId] ?? 0,
                                'transferred_students' => $transferredCount[$customClassId] ?? 0,
                                'status' => 'promoted',
                                'completed_at' => now(),
                                'updated_at' => now()
                            ]);
                    }
                });
            });

        } catch (\Exception $e) {

            DB::table('class_promotion_status')
                ->where('school_id', $this->schoolId)
                ->where('status', 'processing')
                ->update([
                    'status' => 'failed',
                    'updated_at' => now()
                ]);

            \Log::error('Student Promotion Failed', [
                'school_id' => $this->schoolId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}