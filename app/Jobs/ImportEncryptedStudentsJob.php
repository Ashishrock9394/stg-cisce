<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportEncryptedStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $students = DB::table('students')
            ->where('school_id', '3131')
            ->where('academic_year', '2026-2027')
            ->where('is_encrypted', 0)
            ->orderBy('id')
            ->limit(1)
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        $insertData = [];

        foreach ($students as $student) {

            $insertData[] = [

                'school_id' => $student->school_id,
                'school_code' => $student->school_code,

                'student_uid' => $student->student_uid,

                'student_name' => $student->student_name
                    ? Crypt::encryptString($student->student_name)
                    : null,

                'gender' => $student->gender,

                'class_id' => $student->class_id,
                'custom_class_id' => $student->custom_class_id,
                'section_id' => $student->section_id,

                'dob' => $student->dob
                    ? Crypt::encryptString($student->dob)
                    : null,

                'user_id' => $student->user_id,

                'password' => $student->password,

                'password_generated' => $student->password_generated,

                'email_id' => $student->email_id
                    ? Crypt::encryptString($student->email_id)
                    : null,

                'mobile' => $student->mobile
                    ? Crypt::encryptString($student->mobile)
                    : null,

                'rollno' => $student->rollno,

                'status' => $student->status,

                'academic_year' => $student->academic_year,

                'is_pwd' => $student->is_pwd,

                'profile_picture' => $student->profile_picture,

                'remember_token' => $student->remember_token,

                'batchId' => $student->batchId,
                'batchCreatedAt' => $student->batchCreatedAt,

                'paymentStatus' => $student->paymentStatus,

                'careersPaymentRef' => $student->careersPaymentRef,

                'paidOn' => $student->paidOn,
                'updatedOn' => $student->updatedOn,

                'domicile' => $student->domicile
                    ? Crypt::encryptString($student->domicile)
                    : null,

                'fav_sport' => $student->fav_sport,

                'hobbies' => $student->hobbies,

                'aadhaarId' => $student->aadhaarId,

                'apaarId' => $student->apaarId
                    ? Crypt::encryptString($student->apaarId)
                    : null,

                'passport' => $student->passport,

                'program_id' => $student->program_id,

                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ];
        }

        DB::beginTransaction();

        try {

            DB::table('cisce_students')->insert($insertData);

            DB::table('students')
                ->whereIn('id', $students->pluck('id'))
                ->update([
                    'is_encrypted' => 1
                ]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error($e->getMessage());
        }
    }
}