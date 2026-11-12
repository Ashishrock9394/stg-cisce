<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use DB;

class StudentEmailTemplete implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    WithColumnWidths,
    WithEvents
{
    protected int $schoolId;
    protected array $studentIds;

    public function __construct(int $schoolId,  array $studentIds)
    {
        $this->schoolId = $schoolId;
        $this->studentIds = $studentIds;
    }

    public function query() : Builder|EloquentBuilder|Relation
    {   
        $year = date('Y');
		$month = date('m');
		$academicYear = ($month >= 4)
			? $year . '-' . ($year + 1)
			: ($year - 1) . '-' . $year;

        $query = DB::table('students')
            ->leftJoin('class', 'students.class_id', '=', 'class.id')
            ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
            ->select(
                'students.id',
                'students.student_name',
                'class.name as class_name',
                'custom_classes.nomenclature as custom_class_name',
                'students.section_id',
                'students.rollno',
                'students.email_id'
            );
        if($this->studentIds){
            return $query->whereIn('students.id', $this->studentIds)
            ->orderBy('students.custom_class_id')
            ->orderBy('students.rollno');
        }else{
            return $query->where('students.school_id', (string)$this->schoolId)
            ->where('students.academic_year', $academicYear)
            ->where('students.status', 'active')
            ->orderBy('students.custom_class_id')
            ->orderBy('students.rollno');
        }
        
    }

    public function headings(): array
    {
        return [
            'student_id',
            'Class',
            'Section',
            'Student Name',
            'Roll No',
            'Email Id'
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->custom_class_name ?? $student->class_name,
            $student->section_id,
            $student->student_name,
            $student->rollno,
            $student->email_id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 10,
            'C' => 10,
            'D' => 25,
            'E' => 10,
            'F' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);

                $sheet->setCellValue('A1', $this->schoolId);
                $sheet->getRowDimension(1)->setVisible(false);

                $sheet->setCellValue('A2', "Parent's Email Template");
                $sheet->mergeCells('A2:F2');

                $lastColumn = 'F';

                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A3:E3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFAE59'],
                    ],
                ]);

                $sheet->getStyle("F3")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFD966'],
                    ],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'C0D9F7'],
                    ],
                ]);

                $sheet->getColumnDimension('A')->setVisible(false);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("F4:F{$lastRow}")
                    ->getProtection()
                    ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                $sheet->getStyle("A1:E{$lastRow}")
                    ->getProtection()
                    ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('cisceTestData');

                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('cisceTestData');
            }
        ];
    }
}