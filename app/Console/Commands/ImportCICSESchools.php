<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CICSESchoolImport;


class ImportCICSESchools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cicse-schools {organisation_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CISCE school data from Excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle() {

        $organisation_id = $this->argument('organisation_id');

        $filePath = storage_path('app/cisce/cicse_schools.xlsx');

        if (!file_exists($filePath)) {
            $this->error("Excel file not found at: $filePath");
            return;
        }

        Excel::import(new CICSESchoolImport($organisation_id), $filePath);

        $this->info('CISCE school data import has started. Check your queue worker.');
    }
}
