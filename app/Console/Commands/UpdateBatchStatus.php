<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Sstudent;

class UpdateBatchStatus extends Command
{
    protected $signature = 'update:batch-status';
    protected $description = 'Update batch payment status for career portal';

    public function handle(){

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Batch status update requested with career portal');

        $pendingBatches = Sstudent::select('school_code', 'batchId')
            ->where('paymentStatus', 'pending')
            ->whereNotNull('batchId')
            ->groupBy('school_code', 'batchId')
            ->get();
        $time = now()->toIso8601String();
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

                    //Log::warning("payment revoked by $batch->school_code");

                   /* Log::error('Fetch Batch Status API Failed', [
                        'schoolcode' => $batch->school_code,
                        'batchId' => $batch->batchId,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);*/

                    continue;
                }

                $data = $response->json();

                $status = strtolower($data['batchStatus'] ?? '');
                $paidOn = Carbon::parse($data['lastUpdatedAt'])->format('Y-m-d H:i:s');

                if ($status === 'success') {

                    Sstudent::where('school_code', $batch->school_code)
                        ->where('batchId', $batch->batchId)
                        ->where('paymentStatus', 'pending')
                        ->update([
                            'paymentStatus'     => 'paid',
                            'careersPaymentRef' => $data['careersOnlinePaymentID'],
                            'gatewayTxnId'      => $data['gatewayTxnId'],
                            'paidOn'            => $paidOn,
                            'updatedOn'         => $time,
                        ]);
                }elseif (in_array($status, ['failed', 'failure'])) {

                    Sstudent::where('school_code', $batch->school_code)
                    ->where('batchId', $batch->batchId)
                        ->update([
                            'batchId'            => null,
                            'batchCreatedAt'     => null,
                            'paymentStatus'      => 'unpaid',
                            'careersPaymentRef'  => null,
                            'gatewayTxnId'       => null,
                            'paidOn'             => null,
                            'updatedOn'          => $time,
                        ]);
                }

            } catch (\Exception $e) {

                Log::error('Batch Status Sync Failed', [
                    'batchId' => $batch->batchId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Batch status sync completed');
    }
}

