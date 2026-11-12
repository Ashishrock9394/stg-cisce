<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Illuminate\Support\Enumerable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

use DB;
use App\Models\TermMaster;

class TestScoreTemplete implements 
    
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    WithColumnWidths,
    WithEvents
{

    protected array $studentIds;
    protected int $skillId;
    protected string $skillName;
    protected int $schoolId;
    protected string $status;

    protected int $termMasterId;
    protected int $skillTypeCount = 0;
    protected array $skillTypeDescriptions = [];


    public function __construct(array $studentIds, string $skillName, int $skillId, int $schoolId, string $status)
    {
        
        $this->studentIds = $studentIds;
        $this->skillId    = $skillId;
        $this->skillName  = $skillName;
        $this->schoolId   = $schoolId;
        $this->status     = $status;

        $this->boot();
    }   

    protected function boot(): void {
    
        $today = now()->startOfDay();

        $year = date('Y');
        $month = date('m');
        $academicYear = ($month >= 4)
            ? $year . '-' . ($year + 1)
            : ($year - 1) . '-' . $year;

        $termId = TermMaster::where('school_id', $this->schoolId)
            ->where('is_active', 1)
            ->where('academic_year', $academicYear)
            ->value('id');

        if (!$termId) {
            throw new \Exception(
                "No active term found for school_id {$this->schoolId} on date {$today->toDateString()}"
            );
        }

        $this->termMasterId = $termId;



        if ($this->skillId >= 1 && $this->skillId <= 15) {
            $skillTypes = DB::table('skill_types')
                ->where('skill_report_id', $this->skillId)
                ->where('status', 1)
                ->get(['description']);

            $this->skillTypeCount        = $skillTypes->count();
            $this->skillTypeDescriptions = $skillTypes->pluck('description')->toArray();
        }
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
                DB::raw("CASE 
                    WHEN custom_classes.nomenclature IS NOT NULL 
                        AND custom_classes.nomenclature <> '' 
                    THEN custom_classes.nomenclature 
                    ELSE class.name 
                END AS display_classname")
            )
            ->whereIn('students.id', $this->studentIds)
            ->where('students.status', 'active');



        if ($this->status !== 'all') {
            $query->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('SeniorTestResults as r')
                        ->whereColumn('r.StudentID', 'students.id')
                        ->where('r.TestTypeID', $this->skillId)
                        ->where('r.TermId', $this->termMasterId);
                })
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('skillreport_skilltype_termtype_mapping as m')
                        ->whereColumn('m.student_id', 'students.id')
                        ->where('m.skill_report_id', $this->skillId)
                        ->where('m.term_master_id', $this->termMasterId);
                });
        }

        return $query->orderBy('class.orders')
                ->orderBy('custom_classes.section')->orderBy('students.rollno')->get();
    }

    public function headings(): array
    {
        $fixed = [
            'student_id',
            'Class',
            'Section',
            'Student Name',
            'Roll No'
        ];


        if ($this->skillId >= 1 && $this->skillId <= 15) {
            return array_merge($fixed, ['Status'], $this->skillTypeDescriptions);
        }

        return match (true) {
            in_array($this->skillId, [18]) => array_merge($fixed, ['height_cm_mm', 'weight_kg_gm']),
            in_array($this->skillId, [22]) => array_merge($fixed, ['initial_position_cm_mm', 'final_position_cm_mm']),
            in_array($this->skillId, [16, 19, 20]) => array_merge($fixed, ['min', 'sec', 'ms']),
            in_array($this->skillId, [17, 21, 23]) => array_merge($fixed, ['count']),
            default => array_merge($fixed, ['Score']),
        };




        /*$skillTypes = DB::table('skill_types')
            ->where('skill_report_id', $this->skillId)
            ->where('status', 1)
            ->get();

        if ($this->skillId == 18) { return array_merge($fixedHeaders, ['height_cm_mm', 'weight_kg_gm',]);
        } elseif ($this->skillId == 22) {
            return array_merge($fixedHeaders, [
                'initial_position_cm_mm',
                'final_position_cm_mm',
            ]);
        } elseif ($this->skillId >= 1 && $this->skillId <= 15) {
            return array_merge(
                $fixedHeaders,
                ['Status'],
                $skillTypes->pluck('description')->toArray(),
            );
        } elseif (in_array($this->skillId, [16, 19, 20])) {
            return array_merge($fixedHeaders, [
                'min',
                'sec',
                'ms',
            ]);
        } elseif (in_array($this->skillId, [17, 21, 23])) {
            return array_merge($fixedHeaders, [
                'count',
            ]);
        }

        return array_merge($fixedHeaders, ['Score']);*/
    }

    public function map($student): array
    {
        $row = [
            $student->id,
            $student->display_classname,
            $student->section,
            $student->student_name,
            $student->rollno,
        ];

        if ($this->skillId >= 1 && $this->skillId <= 15) {
            $row[] = 'Not Attempted';
            for ($i = 0; $i < $this->skillTypeCount; $i++) {
                $row[] = null;
            }
        } else {
            $row[] = null;
        }

        return $row;
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
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25,
            'K' => 25,
        ];
    }


    public function registerEvents(): array{

        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);
                $highestRow = $sheet->getHighestRow();

                $sheet->setCellValue('A1', $this->skillId);
                $sheet->getRowDimension(1)->setVisible(false);

                $sheet->setCellValue('A2', $this->skillName);

                if ($this->skillId >= 1 && $this->skillId <= 15) {
                    $sheet->mergeCells('A2:K2');

                    $validation = new DataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"Attempted,Not Attempted"');
                    $validation->setErrorTitle('Invalid Input');
                    $validation->setError('Only Attempted or Not Attempted allowed');
                    
                    for ($row = 4; $row <= $highestRow; $row++) {
                        $sheet->getCell("F{$row}")->setDataValidation(clone $validation);
                        $sheet->setCellValue("F{$row}", 'Not Attempted');
                    }

                } elseif (in_array($this->skillId, [18, 22])) {
                    $sheet->mergeCells('A2:G2');
                } elseif (in_array($this->skillId, [16, 19, 20])) {
                    $sheet->mergeCells('A2:H2');
                } else {
                    $sheet->mergeCells('A2:F2');
                }

                // $lastColumn = $sheet->getHighestColumn();
                $mergedCells = $sheet->getMergeCells();

                foreach ($mergedCells as $range) {
                    [$start, $end] = explode(':', $range);
                    preg_match('/[A-Z]+/', $end, $matches);
                    $lastColumn = $matches[0];
                }

                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'wrapText' => true,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3:E3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFAE59'],
                    ],
                ]);

                $sheet->getStyle("F3:{$lastColumn}3")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFD966'],
                    ],
                ]);

                $sheet->getRowDimension(3)->setRowHeight(-1);

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
                $lastRow = max(4, $sheet->getHighestRow());
                $sheet->getStyle("F4:{$lastColumn}{$lastRow}")
                ->getProtection()
                ->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
                
                $sheet->setAutoFilter("A3:{$lastColumn}{$highestRow}");
                
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('cisceTestData');

                /*$sheet->getColumnDimension('A')->setVisible(false);
                $sheet->getStyle("A1:{$lastColumn}1000")->getProtection()->setLocked(true);
                $sheet->getStyle("F4:{$lastColumn}1000")->getProtection()->setLocked(false);*/

                $sheet->getProtection()->setSort(false);
                $sheet->getProtection()->setInsertRows(false);
                $sheet->getProtection()->setInsertColumns(false);
                $sheet->getProtection()->setDeleteRows(false);
                $sheet->getProtection()->setDeleteColumns(false);
                $sheet->getProtection()->setFormatCells(false);
            }
        ];
    }

}
