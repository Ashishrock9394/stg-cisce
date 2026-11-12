<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use \Illuminate\Support\Enumerable;

class ExportTestImproperData implements 
    FromCollection,
    WithHeadings,
    WithEvents,
    WithColumnWidths
{
    protected $data;
    protected $skillId;
    protected $skillName;
    protected $headings;

    public function __construct($data, $skillId, $skillName){
        $this->data      = collect($data);
        $this->skillId   = $skillId;
        $this->skillName = $skillName;

        $firstRow = $this->data->first();

        $validKeys = [];

        foreach ($firstRow as $key => $value) {
            if ($key === 'Error' || $key === 'student_id') {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $validKeys[] = $key;
        }
        $this->headings = array_merge($validKeys, ['Error']);

    }


    public function collection() :Enumerable
    {
        return $this->data->map(function ($row) {
            return collect($this->headings)->map(fn ($key) => $row[$key] ?? '');
        });
    }



    public function headings(): array
    {
        return array_map(function($heading) {
            return ucwords(str_replace('_', ' ', $heading));
        }, $this->headings);
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->headings as $index => $heading) {
            $widths[Coordinate::stringFromColumnIndex($index + 1)] =
                $heading === 'Error' ? 60 : 25;
        }
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Insert row for skill name
                $sheet->insertNewRowBefore(1, 1);

                $lastColumn = $sheet->getHighestColumn();

                // Skill name in A1
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', $this->skillName);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'ADD8E6'],
                    ],
                ]);

                // Header style (row 2)
                $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'ffae59'],
                    ],
                    'alignment' => [
                        'wrapText' => true,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Error column header RED
                $errorColumn = Coordinate::stringFromColumnIndex(count($this->headings));
                $sheet->getStyle($errorColumn . '2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FF0000'],
                    ],
                ]);

                $sheet->freezePane('A3');
            }
        ];
    }

}
