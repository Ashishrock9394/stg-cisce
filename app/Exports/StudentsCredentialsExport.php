<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Illuminate\Support\Enumerable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use DB;

class StudentsCredentialsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithColumnFormatting, 
    WithColumnWidths,
    WithEvents
{
    protected $studentIds;
    protected $className;
    protected $sectionName;

    public function __construct(array $studentIds, string $className = null, string $sectionName = null)
    {
        $this->studentIds = $studentIds;
        $this->className = $className;
        $this->sectionName = $sectionName;
    }

    public function collection() : Enumerable
    {
        $query = DB::table('students')
            ->leftJoin('class', 'students.class_id', '=', 'class.id')
            ->leftJoin('custom_classes', 'students.custom_class_id', '=', 'custom_classes.id')
            ->select(
                'students.id',
                'students.student_name',
                'class.name as class_name',
                'custom_classes.nomenclature as custom_class_name',
                'custom_classes.section',
                'students.rollno',
                'students.student_uid',
                'students.user_id',
                'students.password',
                DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                END AS display_classname")
            )
            ->whereIn('students.id', $this->studentIds);

        if ($this->className) {
            $query->where(function ($q) {
                $q->where('class.name', $this->className)
                    ->orWhere('custom_classes.nomenclature', $this->className);
            });
        }

        if ($this->sectionName) {
            $query->where('custom_classes.section', $this->sectionName);
        }

        return $query->orderBy('custom_classes.orders')
            ->orderBy('custom_classes.section')
            ->orderBy('students.rollno')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Class',
            'Section',
            'Roll No',
            'Student Name',
            'Username',
            'Password',
        ];
    }

    public function map($student): array
    {  
        $firstName = strtolower(trim(explode(' ', $student->student_name)[0]));
        $plainPassword = $firstName . '@' . trim($student->student_uid);
        return [
            $student->display_classname,
            $student->section,
            $student->rollno,
            $student->student_name,
            (string) $student->user_id,
            $plainPassword
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
            'A' => 12,
            'B' => 10,
            'C' => 10,
            'D' => 25,
            'E' => 25,
            'F' => 25,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE0E0E0'],
                ],
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 1);

                $sheet->setCellValue('A1', 'Students Login Credentials');

                $sheet->mergeCells('A1:F1');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '4A90E2'],
                    ]
                ]);
            }
        ];
    }
}
