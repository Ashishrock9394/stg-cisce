<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Enumerable;

class TrainerCredentials implements FromCollection, WithHeadings, WithEvents
{
    protected $TrainerIds;

    public function __construct(array $TrainerIds)
    {
        $this->TrainerIds = $TrainerIds;
    }

    public function collection() : Enumerable
    {
        $userID = Auth::user()->id;
        $schoolId = DB::table('school_reference')->where('school_user_id',$userID)->value('school_id');

        $activeTrainers = DB::table('school_trainers')
        ->whereIn('trainer_id', $this->TrainerIds)
        ->where('school_id',$schoolId)
        ->where('status', 1)->pluck('trainer_id');

        $users = DB::table('users')
            // ->whereIn('id', $this->TrainerIds)
            ->whereIn('id', $activeTrainers)
            ->get();

        $data = [];

        foreach ($users as $user) {
            $usermeta = DB::table('usermetas')
                ->where('user_id', $user->id)
                ->first();

            $status = DB::table('school_trainers')
                ->where('trainer_id', $user->id)
                ->where('school_id', $schoolId)
                ->value('status');

            $dob = $usermeta->dob ?? null;

            $password = '';

            if ($dob) {

                if (is_numeric($dob)) {
                    $dobCarbon = Carbon::createFromTimestamp(($dob - 25569) * 86400);
                    $password = $dobCarbon->format('Ymd');
                    $dob = $dobCarbon->format('Y-m-d');
                } else {
                    $dob = trim($dob);

                    try {
                        $password = Carbon::parse($dob)->format('Ymd');
                    } catch (\Exception $e) {
                        $password = '';
                    }
                }
            }

            $data[] = [
                'TrainerId'   => $user->self_registrationId ?? '',
                'Name'        => $user->name,
                'Email'       => $user->email,
                'Phone'       => $user->phone,
                'Designation' => $user->position ?? '',
                'DOB'         => $dob ?? 'NA',
                'User Name'   => $user->email,
                'Password'    => $password,
                'Status'      => $status == 1 ? 'Mapped' : 'Unmapped',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'TrainerId',
            'Name',
            'Email',
            'Phone',
            'Designation',
            'DOB',
            'User Name',
            'Password',
            'Status'
        ];
    }

    /**
     * Apply events to sheet (column width + style)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 1);

                $sheet->setCellValue('A1', 'Trainer Credentials'); 

                // Merge A1 to I1 (cover all columns)
                $sheet->mergeCells('A1:I1');

                // Style the header
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'size' => 16,
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '4A90E2'],
                    ]
                ]);

                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(30);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(12);

                $highestRow = $sheet->getHighestDataRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $dobCell = 'F' . $row;
                    $statusCell = 'I' . $row;

                    if ($sheet->getCell($dobCell)->getValue() === 'NA') {
                        $sheet->getStyle($dobCell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => 'FF0000'],
                                'bold' => true
                            ]
                        ]);
                    }

                    if ($sheet->getCell($statusCell)->getValue() === 'Unmapped') {
                        $sheet->getStyle($statusCell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => 'FF0000'],
                                'bold' => true
                            ]
                        ]);
                    }
                }                

                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true]
                ]);
            }
        ];
    }
}
