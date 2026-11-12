<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRequest extends Model
{
    use HasFactory;

    protected $fillable = ['school_id','student_id','batch_id','status','requested_at','generated_at','file_path'];



     /* Relationships*/
    public function school() {
        return $this->belongsTo(School::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function batch() {
        return $this->belongsTo(ReportBatch::class, 'batch_id');
    }

    /*Scope: Fetch only pending or processing requests*/
    public function scopePending($query) {
        return $query->whereIn('status', ['requested', 'processing']);
    }

    /*Scope: Fetch generated reports*/
    public function scopeGenerated($query) {
        return $query->where('status', 'generated');
    }

    /*Utility method — helps in jobs or controllers*/
    public static function markGenerated($studentId, $filePath = null) {
        static::where('student_id', $studentId)
            ->update([
                'status' => 'generated',
                'file_path' => $filePath,
                'generated_at' => now(),
            ]);
    }

    /*Optional helper: prevent duplicate request*/
    public static function canGenerate($schoolId, $studentId) {

        return ! static::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['requested', 'processing'])
            ->exists();
    }

}
