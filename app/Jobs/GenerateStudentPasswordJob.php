<?php

namespace App\Jobs;

use App\Models\Sstudent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class GenerateStudentPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $studentId;

    public int $timeout = 120;
    public int $tries = 1;

    public function __construct(int $studentId)
    {
        $this->studentId = $studentId;
    }

    public function handle(): void
    {
        $student = Sstudent::select('id', 'student_name', 'student_uid', 'password_generated')
            ->where('id', $this->studentId)
            ->first();

        if (!$student || $student->password_generated) {
            return; // already processed
        }

        // Generate password securely
        $firstName = strtolower(trim(explode(' ', $student->student_name)[0]));
        $plainPassword = $firstName . '@' . trim($student->student_uid);

        $student->update([
            'password' => Hash::make($plainPassword),
            'password_generated' => 1
        ]);
    }
}
