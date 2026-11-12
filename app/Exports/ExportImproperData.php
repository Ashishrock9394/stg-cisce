<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;
use \Illuminate\Support\Enumerable;

class ExportImproperData implements FromCollection, WithHeadings, WithEvents , WithColumnFormatting
{


    private $data;
    private $error_message;
    protected $action;
 
    public function startCell(): string {
        return 'A1';
    }

    public function __construct($data, $action) {

        $this->data = $data;
        $this->action = $action;
    }

    public function collection() : Enumerable {
        //return collect($this->data);

        return collect($this->data)->map(function($item) {
            return [

                $item['school_code'] ?? '',
                $item['admissionnumber'] ?? '',
                $item['name'] ?? '',
                $item['gender'] ?? '',
                $item['class'] ?? '',
                $item['section'] ?? '',
                $item['roll_no'] ?? '',
                $item['dob_ddmmyyyy'] ?? '',
                $item['email'] ?? '',
                $item['rpwd'] ?? '',
                //isset($item['aadhaarid']) ? "'" . $item['aadhaarid'] : '',
                isset($item['apaarid']) ? "'" . $item['apaarid'] : '',
                //isset($item['passportnumber']) ? "'" . $item['passportnumber'] : '',
                $item['Error'] ?? '',
            ];
        });

    }

    public function columnFormats(): array {
        return [
            'I' => '@',  // Email
            'J' => '@',  // ApaarId
        ];
    }

    public function headings(): array {

        if($this->action == 'duplicate'){
            return ['SchoolCode','AdmissionNumber ','Name','Gender', 'Class' ,'Section','Roll No','DOB(DD/MM/YYYY)','Email','CWSN','ApaarID'];
        }else{
           return ['SchoolCode','AdmissionNumber ','Name','Gender', 'Class' ,'Section','Roll No','DOB(DD/MM/YYYY)','Email','CWSN','ApaarID','Error'];
        }
    }
 
    public function registerEvents(): array {

        return [
            AfterSheet::class    => function(AfterSheet $event) { 

                $sheet = $event->sheet->getDelegate();                
                $headerStyle = [
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => '4F81BD'], // Blue color
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'], // White text
                    ],
                ];

                $errorHeaderStyle = [
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FF0000'], // Red color
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'], // White text
                    ],
                ];

                $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
                if($this->action == 'error_list'){
                    $sheet->getStyle('L1')->applyFromArray($errorHeaderStyle);
                }
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(16);    //school code
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(21);    //student admission no.
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);    //Student Name
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(11);    //Gender
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);    //Class
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(11);    //Section
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(10);    //Roll No.
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);    //DOB
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(43);    //Email
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(27);    //Email
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(27);    //ApaarID
                if($this->action == 'error_list'){
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(80); 
                }
            },
        ];
    }

}
