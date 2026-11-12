<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\StudentEncryptionHelper;

class ImportEncryptedStudents extends Command
{
    /**
     * Command Signature
     */
    protected $signature = 'students:import-encrypted';

    /**
     * Command Description
     */
    protected $description = 'Import students into cisce_students with encryption';

    /**
     * Execute Command
     */
    public function handle()
    {
        $this->info('Encrypted student import started.');

        DB::disableQueryLog();

        try {

            DB::table('students')
                // ->where('academic_year', '2026-2027')
                ->where('is_encrypted', '0')
                ->orderBy('id')
                ->chunkById(1000, function ($students) {

                    $insertData = [];

                    foreach ($students as $student) {

                        $insertData[] = [

                            'id' => $student->id,

                            // NORMAL FIELDS
                            'school_id' => $student->school_id,
                            'school_code' => $student->school_code,
                            'student_uid' => $student->student_uid,
                            'gender' => $student->gender,
                            'class_id' => $student->class_id,
                            'custom_class_id' => $student->custom_class_id,
                            'section_id' => $student->section_id,
                            'password' => $student->password,
                            'password_generated' => $student->password_generated,
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
                            'domicile' => $student->domicile,
                            'fav_sport' => $student->fav_sport,
                            'hobbies' => $student->hobbies,
                            'created_at' => $student->created_at,
                            'updated_at' => now(),
                            'aadhaarId' => $student->aadhaarId,
                            'passport' => $student->passport,
                            'program_id' => $student->program_id,

                            // ENCRYPTED FIELDS
                            'student_name' => StudentEncryptionHelper::encryptValue($student->student_name),
                            'mobile' => StudentEncryptionHelper::encryptValue($student->mobile),
                            'email_id' => StudentEncryptionHelper::encryptValue($student->email_id),                            
                            'user_id' => StudentEncryptionHelper::encryptValue($student->user_id),                            
                            'dob' => StudentEncryptionHelper::encryptValue($student->dob),
                            'apaarId' => StudentEncryptionHelper::encryptValue($student->apaarId),

                            'is_encrypted' => '1',
                        ];
                    }

                    if (!empty($insertData)) {

                        DB::table('cisce_students')->upsert(
                            $insertData,
                            ['id']
                        );

                        $studentIds = collect($insertData)
                            ->pluck('id')
                            ->toArray();

                        DB::table('students')
                            ->whereIn('id', $studentIds)
                            ->update([
                                'is_encrypted' => '1'
                            ]);

                        $this->info(count($studentIds) . ' students imported.');
                    }
                });

            $this->info('Encrypted student import completed successfully.');

            Log::info('Encrypted student import completed.');

        } catch (\Exception $e) {

            Log::error('Student import failed: ' . $e->getMessage());

            $this->error($e->getMessage());
        }
    }
}