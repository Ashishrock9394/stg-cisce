<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Sstudent;

class UpdatePaymentDetails extends Command
{
    protected $signature = 'update:payment-details-table';
    protected $description = 'Update payment-details-table for career portal';

    public function handle(){

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] payment-details-table');

        $pendingBatches = DB::table('payment_details')->select('school_code', 'batchId')
            ->where('paid_count', null)
            ->whereNotNull('batchId')
            ->groupBy('school_code', 'batchId')
            ->limit(20)
            ->get();
        $time = now()->toIso8601String();

        $this->info(
            'Batch IDs: ' . $pendingBatches->pluck('batchId')->implode(', ')
        );

        foreach ($pendingBatches as $batch) {            
            try {

                $response = Http::withToken(config('services.careers.token'))
                    ->timeout(30)
                    ->get(
                        config('services.careers.url').'/api/fetchbatchstatus',
                        [
                            'schoolcode' => $batch->school_code,
                            'batchId'    => $batch->batchId,
                        ]
                    );

                if (!$response->successful()) {

                    Log::warning("payment revoked by $batch->batchId");

                    continue;
                }

                $data = $response->json();

                $status = strtolower($data['batchStatus'] ?? '');
                $paidOn = Carbon::parse($data['lastUpdatedAt'])->format('Y-m-d H:i:s');

                if ($status === 'success') {

                    DB::table('payment_details')->where('batchId', $batch->batchId)
                        ->update([
                            'batchCreatedAt'    => $paidOn,
                            'amount'            => $data['amountPaid'],
                            'paid_count'        => (int) ($data['amountPaid'] / 100),
                            'careersPaymentRef' => $data['careersOnlinePaymentID'],
                            'gatewayTxnId'      => $data['gatewayTxnId'],
                            'paidOn'            => $paidOn,
                            'is_synced'         => 1
                        ]);

                    $this->info("[" . now()->format('Y-m-d H:i:s') . "] payment-details-table for {$batch->batchId}");
                }

            } catch (\Exception $e) {

                Log::error('payment-details-table Sync Failed', [
                    'batchId' => $batch->batchId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] payment-details-table sync completed');
    }
}

