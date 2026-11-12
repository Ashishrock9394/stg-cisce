<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use DB;
use App\Models\School;
use App\Models\Sstudent;
use App\Models\ScustomClass;

class PromoteStudentsByIdsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $studentIds;
    protected $schoolId;
    protected $promotionId;

    public function __construct($studentIds, $schoolId, $promotionId)
    {
        $this->studentIds = $studentIds;
        $this->schoolId = $schoolId;
        $this->promotionId = $promotionId;
    }

    public function handle()
    {
        try {
            DB::table('students_promotion_status')
                ->where('id', $this->promotionId)
                ->update([
                    'status' => 'processing',
                    'started_at' => now()
                ]);

            $year  = date('Y');
            $month = date('m');

            $academicYear = ($month >= 4)
                ? "$year-" . ($year + 1)
                : ($year - 1) . "-$year";

            $classes = DB::table('class')
                ->orderBy('orders')
                ->pluck('id')
                ->toArray();

            $nextClassMap = [];
            foreach ($classes as $i => $classId) {
                $nextClassMap[$classId] = $classes[$i + 1] ?? null;
            }

            $customClasses = DB::table('custom_classes')
                ->where('school_id', $this->schoolId)
                ->get()
                ->keyBy(fn($c) => $c->class_id . '_' . $c->section);

            $maxOrder = ScustomClass::where('school_id', $this->schoolId)->max('orders') ?? 0;

            $promotedCount = 0;
            $transferredCount = 0;

            Sstudent::whereIn('id', $this->studentIds)
                ->where('status', 'active')
                ->chunkById(500, function ($students) use (
                    &$customClasses,
                    &$maxOrder,
                    $nextClassMap,
                    $academicYear,
                    &$promotedCount,
                    &$transferredCount
                ) {
                    try {

                        foreach ($students as $student) {

                            if ($student->class_id == 12) {
                                $student->update([
                                    'status' => 'transfer',
                                ]);
                                $transferredCount++;
                                continue;
                            }
                            $nextClassId = $nextClassMap[$student->class_id] ?? null;
                            if (!$nextClassId) continue;

                            $key = $nextClassId . '_' . $student->section_id;

                            if (!isset($customClasses[$key])) {
                                $maxOrder++;

                                $customClasses[$key] = ScustomClass::create([
                                    'school_id'    => $this->schoolId,
                                    'class_id'     => $nextClassId,
                                    'section'      => $student->section_id,
                                    'nomenclature' => 'Class ' . $nextClassId,
                                    'orders'       => $maxOrder,
                                    'status'       => 1,
                                ]);
                            }

                            $student->update([
                                'status'        => 'promoted',
                            ]);

                            Sstudent::create([
                                'school_id'         => $this->schoolId,
                                'school_code'       => $student->school_code,
                                'student_uid'       => $student->student_uid,
                                'student_name'      => $student->student_name,
                                'gender'            => $student->gender,
                                'class_id'          => $nextClassId,
                                'custom_class_id'   => $customClasses[$key]->id,
                                'section_id'        => $student->section_id,
                                'dob'               => $student->dob,
                                'user_id'           => $student->user_id,
                                'password'          => $student->password,
                                'email_id'          => $student->email_id,
                                'rollno'            => $student->rollno,
                                'domicile'          => $student->domicile,
                                'fav_sport'         => $student->fav_sport,
                                'hobbies'           => $student->hobbies,
                                'apaarId'           => $student->apaarId,
                                'aadhaarId'         => $student->aadhaarId,
                                'passport'          => $student->passport,
                                'status'            => 'active',
                                'academic_year'   => $academicYear,
                            ]);

                            $promotedCount++;
                        }
                        DB::table('students_promotion_status')
                            ->where('id', $this->promotionId)
                            ->update([
                                'promoted_students' => $promotedCount,
                                'transferred_students' => $transferredCount
                            ]);

                    } catch (\Exception $e) {
                        Log::error('Chunk failed: ' . $e->getMessage());
                    }
                });
            DB::table('students_promotion_status')
                ->where('id', $this->promotionId)
                ->update([
                    'status' => 'promoted',
                    'completed_at' => now()
                ]);
        } catch (\Exception $e) {
            DB::table('students_promotion_status')
                ->where('id', $this->promotionId)
                ->update([
                    'status' => 'failed'
                ]);

            Log::error('Promotion failed: ' . $e->getMessage());
        }
    }
}
