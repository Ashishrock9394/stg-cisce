<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class DateHelper
{
    /**
     * Validate and normalize DOB.
     * Accepts:
     *  - Excel serial date (20000–60000 range)
     *  - dd/mm/yyyy
     *  - dd-mm-yyyy
     * 
     * @return array ['date' => string|null (Y-m-d), 'corrected' => bool]
     */
    public static function parseDob(?string $value): array
    {

        $result = [
            'date' => null,
            'corrected' => false
        ];

        if (empty($value)) {
            return $result;
        }

        $value = trim($value);

        // Case 1: Excel numeric date format
        if (is_numeric($value) && (int)$value > 20000 && (int)$value < 60000) {
            try {
                $result['date'] = ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
                return $result;
            } catch (\Exception $e) {
                return $result;
            }
        }

        // Case 2: dd/mm/yyyy
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            try {
                $carbonDate = Carbon::createFromFormat('d/m/Y', $value);
                $result['date'] = $carbonDate->format('Y-m-d');
                return $result;
            } catch (\Exception $e) {
                return $result;
            }
        }

        // Case 3: dd-mm-yyyy → convert to dd/mm/yyyy
        if (preg_match('/^\d{2}\-\d{2}\-\d{4}$/', $value)) {
            try {
                $fixed = str_replace('-', '/', $value);
                $carbonDate = Carbon::createFromFormat('d/m/Y', $fixed);
                $result['date'] = $carbonDate->format('Y-m-d');
                $result['corrected'] = true;
                return $result;
            } catch (\Exception $e) {
                return $result;
            }
        }

        return $result; // Invalid format
    }
}
