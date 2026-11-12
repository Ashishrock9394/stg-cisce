<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentEmailErrorExport implements
    FromArray,
    WithHeadings,
    WithColumnWidths,
    WithEvents
{
    protected $rows;
    protected $schoolId;

    public function __construct($rows, $schoolId = null)
    {
        $this->rows = $rows;
        $this->schoolId = $schoolId;
    }

    public function headings(): array
    {
        return [
            'Class',
            'Section',
            'Student Name',
            'Roll No',
            'Email Id',
            'Error Message'
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 25,
            'D' => 10,
            'E' => 25,
            'F' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Insert title row
                $sheet->insertNewRowBefore(1, 1);

                $sheet->setCellValue('A1', 'Student Email Error Report');
                $sheet->mergeCells('A1:F1');

                // Title Style
                $sheet->getStyle('A1')->applyFromArray([
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

                // Header Style
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A2:E2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFAE59'],
                    ],
                ]);

                $sheet->getStyle('F2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FF6666'],
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:F{$lastRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        ];
    }
}