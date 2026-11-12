<?php  

namespace App\Traits;
use App\Models\TermMaster;
use App\Models\TotTrainerMapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolTrainer;

trait TotHelpersTrait {

	/* Generating Class and Section For ToT School */
    private function mapClassesToTotSchool($originalSchoolId, $totSchoolId, $programId)  {

        $classMappings = [];
        
        // Get all classes from the original school
        $originalClasses = DB::table('custom_classes')
            ->where('school_id', $originalSchoolId)->where('status', 1)
            ->get();
        
        foreach ($originalClasses as $originalClass) {

            $existingClass = DB::table('custom_classes')
                ->where('school_id', $totSchoolId)
                ->where('nomenclature', $originalClass->nomenclature)
                ->first();
            
            if ($existingClass) {
                // Use existing class
                $classMappings[$originalClass->id] = [
                    'custom_class_id' => $existingClass->id,
                    'class_id' => $existingClass->class_id,
                    'section' => $originalClass->section
                ];
            } else {
                // Create new class in TOT school
                $newClassId = DB::table('custom_classes')->insertGetId([
                    'school_id' => $totSchoolId,
                    'class_id' => $originalClass->class_id,
                    'nomenclature' => $originalClass->nomenclature,
                    'section' => $originalClass->section,
                    'status'  => $originalClass->status,
                ]);
                
                $classMappings[$originalClass->id] = [
                    'custom_class_id' => $newClassId,
                    'class_id' => $originalClass->class_id,
                    'section' => $originalClass->section
                ];
            }
        }
        
        return $classMappings;
    }

    /**
     * Process existing trainer
     */
    private function processExistingTrainer($user, $name, $email, $mobile, $gender, $dob, $qualification, $state, $district, $city, $address,
        $totProgram, $totSchoolId) {

        $trainerId = $user->id;
        
        // Get all ACTIVE mappings to other schools
        $activeSchoolMappings = SchoolTrainer::where('trainer_id', $trainerId)
            ->where('status', 1)
            ->where('school_id', '!=', $totSchoolId)
            ->get();

        $originalMappingsData = [];

        // Unmap from all other active schools
        foreach ($activeSchoolMappings as $mapping) {
            // FIX: Handle both Carbon instance and string formats
            $mappedOn = $mapping->mapped_on;
            if ($mappedOn instanceof \Carbon\Carbon) {
                $mappedOn = $mappedOn->toDateTimeString();
            } else {
                $mappedOn = (string) $mappedOn; // Convert to string if it's already a string
            }

            $originalMappingsData[] = [
                'school_id' => $mapping->school_id,
                'original_status' => $mapping->status,
                'mapped_on' => $mappedOn, // Use the properly formatted date
                'unmapped_on' => now()->toDateTimeString()
            ];

            $mapping->update([
                'status' => 0,
                'unmapped_on' => now()
            ]);
        }

        // Ensure mapping to TOT school exists and is active
        $totSchoolMapping = SchoolTrainer::firstOrCreate(
            [
                'trainer_id' => $trainerId,
                'school_id' => $totSchoolId
            ],
            [
                'status' => 1,
                'mapped_on' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($totSchoolMapping->status == 0) {
            $totSchoolMapping->update([
                'status' => 1,
                'mapped_on' => now()
            ]);
        }

        // Create TOT tracking record
        TotTrainerMapping::create([
            'tot_program_id' => $totProgram->id,
            'trainer_id' => $trainerId,
            'tot_school_id' => $totSchoolId,
            'original_school_data' => !empty($originalMappingsData) ? $originalMappingsData : null,
            'status' => !empty($originalMappingsData) ? 'mapped' : 'new',
            'mapped_on' => now(),
        ]);

        // Update user data
        DB::table('users')->where('id', $trainerId)->update([
            'name' => $name,
            'phone' => $mobile,
            'updated_at' => now()
        ]);

        // Update user metadata
        DB::table('usermetas')->updateOrInsert(
            ['user_id' => $trainerId],
            [
                'gender' => $gender,
                'dob' => $dob,
                'qualification' => $qualification,
                'state' => $state,
                'district' => $district,
                'city' => $city,
                'address' => $address,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Process new trainer
     */
    private function processNewTrainer($name, $email, $mobile, $gender, $dob, $qualification, $state, $district, $city, $address, $totProgram, $totSchoolId) {

        $trainerId = DB::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'phone' => $mobile,
            'position' => 'Trainer',
            'password' => bcrypt('Tot2k25@f365'),
            'role_id' => 3,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Generate registration ID
        $serial = str_pad($trainerId, 7, '0', STR_PAD_LEFT);
        $self_registrationId = 'GFFT' . $serial;

        DB::table('users')->where('id', $trainerId)->update([
            'self_registrationId' => $self_registrationId
        ]);

        // Create user metadata
        DB::table('usermetas')->insert([
            'user_id' => $trainerId,
            'gender' => $gender,
            'dob' => $dob,
            'qualification' => $qualification,
            'state' => $state,
            'district' => $district,
            'city' => $city,
            'address' => $address,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create mapping to TOT school
        SchoolTrainer::create([
            'trainer_id' => $trainerId,
            'school_id' => $totSchoolId,
            'status' => 1,
            'mapped_on' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create TOT tracking record
        TotTrainerMapping::create([
            'tot_program_id' => $totProgram->id,
            'trainer_id' => $trainerId,
            'tot_school_id' => $totSchoolId,
            'original_school_data' => null,
            'status' => 'new',
            'mapped_on' => now(),
        ]);
    }

    /**
     * Helper method to restore a trainer mapping
     */
    private function restoreTrainerMapping(TotTrainerMapping $mapping)  {
        try {
            $trainerId = $mapping->trainer_id;
            $restoredSchools = [];
            $errors = [];

            // 1. Restore original school mappings
            if ($mapping->original_school_data && is_array($mapping->original_school_data)) {
                foreach ($mapping->original_school_data as $originalMapping) {
                    try {
                        // Validate required fields
                        if (empty($originalMapping['school_id'])) {
                            $errors[] = "Missing school_id in original mapping data";
                            continue;
                        }

                        $schoolId = $originalMapping['school_id'];
                        $originalStatus = $originalMapping['original_status'] ?? 1;
                        
                        // Restore the school mapping
                        $schoolTrainer = SchoolTrainer::updateOrCreate(
                            [
                                'trainer_id' => $trainerId,
                                'school_id' => $schoolId
                            ],
                            [
                                'status' => $originalStatus,
                                'mapped_on' => $originalMapping['mapped_on'] ?? now(),
                                'unmapped_on' => null, // Clear unmapped date
                                'updated_at' => now()
                            ]
                        );

                        $restoredSchools[] = $schoolId;


                    } catch (\Exception $e) {
                        $errors[] = "Failed to restore school {$originalMapping['school_id']}: " . $e->getMessage();
                    }
                }
            }

            // 2. Deactivate TOT school mapping
            try {
                $totSchoolUpdate = SchoolTrainer::where('trainer_id', $trainerId)
                    ->where('school_id', $mapping->tot_school_id)
                    ->update([
                        'status' => 0,
                        'unmapped_on' => now(),
                        'updated_at' => now()
                    ]);

                if ($totSchoolUpdate === 0) {
                    Log::warning('No TOT school mapping found to deactivate', [
                        'trainer_id' => $trainerId,
                        'tot_school_id' => $mapping->tot_school_id
                    ]);
                } else {
                    Log::info('Deactivated TOT school mapping', [
                        'trainer_id' => $trainerId,
                        'tot_school_id' => $mapping->tot_school_id
                    ]);
                }

            } catch (\Exception $e) {
                $errors[] = "Failed to deactivate TOT school: " . $e->getMessage();
                Log::error('Error deactivating TOT school', [
                    'trainer_id' => $trainerId,
                    'tot_school_id' => $mapping->tot_school_id,
                    'error' => $e->getMessage()
                ]);
            }

            // 3. Mark TOT mapping as restored
            try {
                $mapping->update([
                    'status' => 'restored',
                    'restored_on' => now(),
                    'updated_at' => now()
                ]);

            } catch (\Exception $e) {
                $errors[] = "Failed to update TOT mapping status: " . $e->getMessage();
            }

            // 4. Prepare result
            if (empty($errors)) {
                return [
                    'success' => true,
                    'message' => sprintf(
                        "Successfully restored trainer to %d school(s) and deactivated TOT school mapping",
                        count($restoredSchools)
                    ),
                    'restored_schools' => $restoredSchools,
                    'restored_count' => count($restoredSchools)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "Partial restoration completed with errors: " . implode('; ', $errors),
                    'restored_schools' => $restoredSchools,
                    'errors' => $errors
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error in restoreTrainerMapping helper', [
                'mapping_id' => $mapping->id,
                'trainer_id' => $mapping->trainer_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Restoration failed: ' . $e->getMessage()
            ];
        }
    }



}