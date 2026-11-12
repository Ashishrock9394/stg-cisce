<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInfo extends Model
{
    use HasFactory;

    protected $table = 'students_info';

    protected $fillable = [
        'student_id', 'age', 'weight', 'height','bmi'
    ];

    public function student()
    {
        return $this->belongsTo(StudentDashboard::class, 'student_id');
    }
}
