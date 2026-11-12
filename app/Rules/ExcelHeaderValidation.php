<?php

namespace App\Rules;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Contracts\Validation\Rule;

class ExcelHeaderValidation implements Rule
{
     private $expectedHeaders = [
        'School Code','AdmissionNumber','Name','Gender','Class','Section','Roll No','DOB (DD/MM/YYYY)','Email','CWSN','ApaarID'];



    public function passes($attribute, $value) {

        try {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($value->getRealPath());
            $actualHeaders = $spreadsheet->getActiveSheet()->toArray()[0];
            
            return $this->expectedHeaders === array_slice($actualHeaders, 0, count($this->expectedHeaders));
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please use the downloaded template without making any modifications. Do not change the headers, formatting, or column order.';
    }
}
