<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CheckAndUpdateBatchStatus extends Command
{
    protected $signature = 'update:check-update-batch';
    protected $description = 'Assign paid batch to active students if seats become available';

    public function handle(){
        try {
            $this->info('Scheduler Started : ' . now());

            $year = date('Y');
            $month = date('m');

            $academicYear = ($month >= 4)
                ? $year . '-' . ($year + 1)
                : ($year - 1) . '-' . $year;
                
            $schools = DB::table('payment_details')
                ->where('is_synced', 0)
                ->select('school_code')
                ->distinct()
                ->orderBy('school_code')
                ->limit(20)
                ->pluck('school_code');

            foreach ($schools as $schoolCode) {

                DB::transaction(function () use ($schoolCode, $academicYear) {

                    DB::table('students')
                        ->where('school_code', $schoolCode)
                        ->update([
                            'paymentStatus' => 'unpaid',
                            'batchId'       => null,
                        ]);

                    $payments = DB::table('payment_details')
                        ->where('school_code', $schoolCode)
                        ->orderBy('paidOn', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($payments as $payment) {
                        $students = DB::table('students')
                            ->where('school_code', $schoolCode)
                            ->where('academic_year', $academicYear)
                            ->where('status', 'active')
                            ->where('paymentStatus', '!=', 'paid')
                            ->orderBy('id', 'asc')
                            ->limit($payment->paid_count)
                            ->get();

                        foreach ($students as $student) {

                            DB::table('students')
                                ->where('id', $student->id)
                                ->update([
                                    'batchId' => $payment->batchId,
                                    'paymentStatus' => 'paid',       
                                    'batchCreatedAt'=> $payment->batchCreatedAt,
                                    'careersPaymentRef'=> $payment->careersPaymentRef,
                                    'gatewayTxnId' => $payment->gatewayTxnId,
                                    'paidOn' => $payment->paidOn,    
                                    'updatedOn' => $payment->paidOn,
                                ]);
                        }
                    }
                });

                DB::table('payment_details')
                    ->where('school_code', $schoolCode)
                    ->update([ 
                        'is_synced'=> 1,
                    ]);

                $this->info("School {$schoolCode} updated successfully.");
            }

            $this->info('Scheduler Completed : ' . now());

            return Command::SUCCESS;

        } catch (\Throwable $e) {

            // Error console par show karega
            $this->error('Scheduler Failed: ' . $e->getMessage());

            // Laravel log mein complete exception save karega
            \Log::error('CheckAndUpdateBatchStatus failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
