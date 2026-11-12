<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sstudent;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Eloquent\Builder;

class ExportStudentProfile implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithEvents
{

    protected $schoolId;
    protected $studentIds;
    protected $selectAll;
    protected $selectedYear;

    public function __construct($schoolId, $studentIds = [], $selectAll = true, $selectedYear) {

        $this->schoolId = $schoolId;
        $this->studentIds = $studentIds;
        $this->selectAll = $selectAll;
        $this->selectedYear = $selectedYear;
    }


    public function query(): Builder {
        $query = Sstudent::query()
            ->with([
                'classData:id,nomenclature,section'
            ])
            ->where('school_id', (string)$this->schoolId)
            ->where('academic_year', (string)$this->selectedYear)
            ->where('status', 'active')

            ->select([
                'id',
                'school_code',
                'student_uid',
                'student_name',
                'gender',
                'class_id',
                'section_id',
                'rollno',
                'dob',
                'email_id',
                'is_pwd',
                'apaarId',
                'custom_class_id'
            ]);

        if (!$this->selectAll && !empty($this->studentIds)) {

            $query->whereIn('id', $this->studentIds);
        }

        return $query->orderBy('custom_class_id')->orderBy('rollno');
    }


    public function headings(): array {
        return [
            'School Code',
            'AdmissionNumber',
            'Name',
            'Gender',
            'Class',
            'Section',
            'Roll No',
            'DOB (DD/MM/YYYY)',
            'Email',
            'CWSN',
            'ApaarID'
        ];
    }


    public function map($student): array {
        return [

            $student->school_code,
            $student->student_uid,
            $student->student_name,
            $student->gender,
            $this->convertNumericToRoman(
                optional($student->classData)->nomenclature
            ),

            optional($student->classData)->section,
            $student->rollno,
            !empty($student->dob)
                ? Carbon::parse($student->dob)->format('d/m/Y')
                : '',
            $student->email_id,
            $student->is_pwd ? 'YES' : 'NO',
            $student->apaarId,
        ];
    }


    protected function convertNumericToRoman($className) {
        $className = trim($className ?? '');

        if (preg_match('/Class\s+(\d+)/i', $className, $matches)) {

            $number = (int) $matches[1];

            $map = [
                10 => 'X',
                9  => 'IX',
                5  => 'V',
                4  => 'IV',
                1  => 'I',
            ];

            $result = '';

            foreach ($map as $value => $roman) {

                while ($number >= $value) {

                    $result .= $roman;

                    $number -= $value;
                }
            }

            return $result;
        }

        return $className;
    }

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    */

    public function chunkSize(): int
    {
        return 500;
    }


    public function registerEvents(): array
{
    return [

        AfterSheet::class => function (AfterSheet $event) {

            $sheet = $event->sheet->getDelegate();

            /*
            |--------------------------------------------------------------------------
            | Freeze Header Row
            |--------------------------------------------------------------------------
            */

            $sheet->freezePane('A2');

            $sheet->getStyle('A1:K1')
                ->getFont()
                ->setBold(true);

            $sheet->getStyle('A1:J1')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFC000',
                    ],
                ],
            ]);

            $sheet->getStyle('K1')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFFF00',
                    ],
                ],
            ]);

            // $sheet->getStyle('A1:K10000')
            //     ->getProtection()
            //     ->setLocked(
            //         \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED
            //     );

            // $sheet->getStyle('A1:K1')
            //     ->getProtection()
            //     ->setLocked(
            //         \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED
            //     );

            // $sheet->getProtection()->setSheet(true);
            // $sheet->getProtection()->setPassword('Seqfast@2k26');
        },
    ];
}
}