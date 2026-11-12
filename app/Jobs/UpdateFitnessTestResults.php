<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateFitnessTestResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $studentId, $termId, $testTypeId, $score, $schoolId , $height, $weight;

    public function __construct($studentId, $termId, $testTypeId, $score, $schoolId, $height, $weight)
    {
        $this->studentId   = $studentId;
        $this->termId      = $termId;
        $this->testTypeId  = $testTypeId;
        $this->score       = $score;
        $this->schoolId    = $schoolId;
        $this->height      = $height;
        $this->weight      = $weight;
    }

    public function handle()
    {

        
        $columns = [
            22 => 'sit_and_reach',
            20 => 'run_600m',
            23 => 'pushups',
            19 => 'dash_50m',
            21 => 'curlup',
            18 => 'bmi',
            // 17 => 'flamingo',
            // 16 => 'plate_tapping'
        ];

        if (!isset($columns[$this->testTypeId])) {
            return; // invalid test type
        }

        $column = $columns[$this->testTypeId];

        $data = [
            'school_id'  => $this->schoolId,
            $column      => $this->score,
            'updated_at' => now(),
        ];

        if (!is_null($this->height)) {
            $data['height'] = $this->height . ' cm';
        }
        if (!is_null($this->weight)) {
            $data['weight'] =  $this->weight . ' kg';
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();


        DB::table('SeniorTestResultsSummary')->updateOrInsert(
            ['student_id' => $this->studentId, 'term_id' => $this->termId],
            $data
        );
    }
}
