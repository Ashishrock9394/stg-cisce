<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InactivateStudentImportLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:inactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactivate Student Import Logs Older Than 5 Days';

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
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(5);

        $updated = DB::table('student_import_logs')
            ->where('created_at', '<', $cutoffDate)
            ->where('is_active', 'active')
            ->update(['is_active' => 'inactive']);

        $this->info("$updated logs marked inactive.");
    }
}
