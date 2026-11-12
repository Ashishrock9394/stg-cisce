<?php

namespace App\Imports;

// use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Exports\FailedRowsExport;

use App\Models\SchoolReference;
use App\Models\CICSESchool;
use App\Models\User;
use App\Models\SchoolUsers;
use Exception;

class CICSESchoolImport implements OnEachRow, WithHeadingRow, WithChunkReading, ShouldQueue
{
    protected $organisation_id;
    
    public function __construct($organisation_id) {

        $this->organisation_id = $organisation_id;
    }

    public function onRow(Row $row) {

        $data = $row->toArray();
        
        // Basic validation
        if (empty($data['school_code']) || empty($data['school_name']) || empty($data['principal_email'])) {
            $this->failedRows[] = $data + ['error' => 'Required fields are missing'];
            return;
        }

        try {

            $exists = DB::table('schools')
                ->where('school_code', $data['school_code'])
                ->where('school_name', $data['school_name'])
                ->exists();

            if (!$exists) {

                $school = CICSESchool::create([
                    'school_code'       => $data['school_code'] ?? null,
                    'school_name'       => $data['school_name'] ?? null,
                    'zonename'          => $data['zone_name'] ?? null,
                    'region'            => $data['region_name'] ?? null,
                    'school_email'      => $data['school_email'] ?? null,
                    'school_principal'  => $data['principal_name'] ?? null,
                    'designation'       => $data['designation'] ?? null,
                    'principal_email'   => $data['principal_email'] ?? null,
                    'city'              => $data['city'] ?? null,
                    'district'          => $data['district'] ?? null,
                    'state'             => $data['state'] ?? null,
                    'address'           => $data['address'] ?? null,
                    'pincode'           => $data['pincode'] ?? null,
                    'shift'             => $data['shift'] ?? null,
                    'contact_number'    => $data['phone'] ?? null,
                    'board'             => 'cisce',
                    'added_date'        => now(),
                    'status'            => 1,
                ]);
            
                
                // Create or reuse User
                $user = User::firstOrCreate(
                    ['email' => $data['principal_email']],
                    [
                        'name'     => $data['principal_name'] ?? 'School Principal',
                        'password' => Hash::make(($data['school_code']) . '@f365'),
                        'userid'   => $data['principal_email'],
                        'position' => 'Principal',
                        'role_id'  => 4,
                    ]
                );

                // Create School Reference
                SchoolReference::create([
                    'school_id'       => $school->id,
                    'school_user_id'  => $user->id,
                    'status'          => 1,
                ]);

                Log::info('Organisation ID:', ['organisation_id' => $this->organisation_id]);
                SchoolUsers::create([
                    'user_id'   => $this->organisation_id,
                    'school_id' => $school->id,
                    'status'    => 1,
                ]);
            }

        } catch (\Exception $e) {

            $this->failedRows[] = $data + ['error' => $e->getMessage()];
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'row'   => $data,
            ]);

        }
    }


    public function __destruct()
    {
        if (!empty($this->failedRows)) {
            $filename = 'failed_rows_' . now()->format('Ymd_His') . '.xlsx';
            $path = 'public/failed-imports/' . $filename;

            // Save failed rows Excel
            Excel::store(new FailedRowsExport($this->failedRows), $path);

            Log::info('Saved failed rows to: storage/app/' . $path);
        }
    }

    public function chunkSize(): int {

        return 200; 
    }

}
