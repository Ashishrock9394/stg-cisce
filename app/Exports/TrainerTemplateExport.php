<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TrainerTemplateExport implements WithHeadings, WithTitle
{
    // Set headers for Excel template
    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Gender',  'DOB',  'Qualification', 'State', 'District', 'City', 'Address'];
    }

    // Optional: sheet title
    public function title(): string
    {
        return 'TrainersPersonalProfile';
    }
}
