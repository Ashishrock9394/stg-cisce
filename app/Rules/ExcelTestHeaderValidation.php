<?php

namespace App\Rules;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Contracts\Validation\Rule;

class ExcelTestHeaderValidation implements Rule
{
    private $expectedHeaders = [
        'student_id',
        'Class', 
        'Section', 
        'Student Name', 
        'Roll No'
    ];

    public function passes($attribute, $value)
    {
        try {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($value->getRealPath());

            $actualHeaders = $spreadsheet->getActiveSheet()->toArray()[2]; 

            return $this->expectedHeaders === array_slice($actualHeaders, 0, count($this->expectedHeaders));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        return 'Please use the downloaded template without making any modifications. Do not change the headers, formatting, or column order.';
    }
}
