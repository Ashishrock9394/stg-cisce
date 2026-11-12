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

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;

class AdminCredentials implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    WithColumnWidths,
    WithEvents
{
    protected $userIds;

    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }

    /**
     * Fetch users + usermeta for export
     */
    public function collection() : Enumerable
    {
        return DB::table('users as u')
            ->leftJoin('usermetas', 'usermetas.user_id', '=', 'u.id')
            ->leftJoin('regions as r', 'r.id', '=', 'usermetas.region_id')
            ->leftJoin('zones as z', 'z.id', '=', 'usermetas.zone_id')
            ->select(
                'u.id',
                'u.name as superadmin_name',
                'u.email',
                'u.phone',
                'u.position',
                'usermetas.gender',
                DB::raw("COALESCE(r.name, 'All') as region_id"),
                DB::raw("COALESCE(z.name, 'All') as zone_id"),
                DB::raw("CASE WHEN u.status = 1 THEN 'Active' ELSE 'Inactive' END as status"),
                DB::raw("
                    CASE
                        WHEN usermetas.designation = 2 THEN 'Super Admin'
                        WHEN usermetas.designation = 5 THEN 'Regional Head'
                        WHEN usermetas.designation = 6 THEN 'Zonal Head'
                        WHEN usermetas.designation = 7 THEN 'Sports Coordinator'
                        ELSE 'N/A'
                    END as designation
                "),
            )
            ->whereIn('u.id', $this->userIds)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Region',
            'Zone',
            'Role',
            'Phone',
            'UserId',
            'Password',
            'Status'
        ];
    }

    public function map($user): array
    {
        $lastFiveDigits = substr(
            preg_replace('/\D/', '', $user->phone),
            -5
        );

        $password = 'Admin@' . $lastFiveDigits;

        return [
            $user->superadmin_name,
            $user->region_id,
            $user->zone_id,
            $user->designation ?? '',
            $user->phone,
            (string) $user->email,
            (string) $password,
            $user->status,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 20,
            'D' => 22,
            'E' => 18,
            'F' => 30,
            'G' => 25,
            'H' => 15,
        ];
    }

    public function styles(Worksheet $sheet) : ?array 
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

                $sheet->setCellValue(
                    'A1',
                    'Admin Login Credentials'
                );

                $sheet->mergeCells('A1:H1');

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
