<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


    Schedule::call(function () {
        file_put_contents(storage_path('logs/schedule-test.log'), now() . "\n", FILE_APPEND);
    })->everyMinute();
    
    // delete report files from local 
    Schedule::command('localreport:delete')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    // Delete upload history from DB
    Schedule::command('delete:upload-history')
    ->dailyAt('23:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    // Clean Generted Report at regular from Azure
    Schedule::command('reports:cleanup')
    ->dailyAt('21:50')
    ->withoutOverlapping(3600) 
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    // delete old-test files from azure 
    Schedule::command('files:delete-old-test-files')
    ->dailyAt('23:10')
    ->appendOutputTo(storage_path('logs/scheduler.log'));


    Schedule::command('students:generate-passwords --chunk=500')
    ->everyTwoHours()
    ->withoutOverlapping(3600) 
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler-password-has.log'))
    ->when(function () {
        return DB::table('school_password_jobs')
            ->where('status', 'pending')
            ->exists();
    });       

    
    Schedule::command('schools:update-assessment')
    ->everyThreeHours()
    // ->dailyAt('10:35')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
    


    Schedule::command('students:import-encrypted')
    ->everyTenMinutes()
    ->when(function () {
        $hour = now()->hour;
        return $hour >= 20 || $hour < 2;
    })
    ->withoutOverlapping(3600)
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(
        storage_path('logs/scheduler.log')
    );

    Schedule::command('students:update-emails')
    ->everyThreeHours()
    ->withoutOverlapping(3600)
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    Schedule::command('update:batch-status')
    // ->dailyAt('16:57')
    ->everyTenMinutes()
    ->withoutOverlapping(3600) 
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    Schedule::command('fitness:flatten-data')
    ->dailyAt('23:30')
    ->withoutOverlapping(3600)
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

    Schedule::command('update:check-update-batch')
    // ->everyTwoMinutes()
    ->everyTwoHours()
    ->withoutOverlapping(3600) 
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/update-payment-scheduler.log'));


    // Schedule::command('sync:senior-test-summary')
    // ->dailyAt('09:55')
    // ->withoutOverlapping(3600) 
    // ->onOneServer()
    // ->runInBackground()
    // ->appendOutputTo(storage_path('logs/scheduler.log'));

    // Schedule::command('sync:lower-fitness-summary')
    // // ->everyTenMinutes()
    // ->dailyAt('10:37')
    // ->withoutOverlapping(3600) 
    // ->onOneServer()
    // ->runInBackground()
    // ->appendOutputTo(storage_path('logs/scheduler.log'));


    // for updating levels in SeniorTestResults table

    // Schedule::command('fitness:update-levels')
    // ->dailyAt('02:30')
    // ->withoutOverlapping(3600) 
    // ->onOneServer()
    // ->runInBackground()
    // ->appendOutputTo(storage_path('logs/scheduler.log'));

    // on year end 
    // Schedule::command('update:term-master')
    // ->dailyAt('09:50')
    // ->appendOutputTo(storage_path('logs/clear-upload-files.log'));

    // for the table 
    
    Schedule::command('update:payment-details-table')
    ->everyTwoHours()
    // ->everyTwoMinutes()
    ->withoutOverlapping(3600) 
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));