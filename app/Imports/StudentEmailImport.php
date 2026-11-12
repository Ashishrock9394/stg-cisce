<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentEmailImport implements ToCollection
{
    protected $batchId;
    protected $schoolId;

    public function __construct($batchId, $schoolId)
    {
        $this->batchId = $batchId;
        $this->schoolId = $schoolId;
    }

    public function collection(Collection $rows) : void
    {
        $rows = $rows->slice(3);

        $insertData = [];

        foreach ($rows as $row) {

            if (empty($row[0])) {
                continue;
            }

            $insertData[] = [
                'batch_id'   => $this->batchId,
                'school_id'  => $this->schoolId,
                'student_id' => $row[0],
                'class'      => $row[1],
                'section'    => $row[2],
                'name'       => $row[3],
                'rollno'     => $row[4],
                'email'      => trim($row[5]),
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($insertData, 1000) as $chunk) {
            DB::table('students_email')->insert($chunk);
        }
    }
}