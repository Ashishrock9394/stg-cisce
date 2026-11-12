<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomClass extends Model
{
    use HasFactory;

    protected $table = 'custom_classes';

    protected $fillable = [
        'school_id', 'class_id', 'section'
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    /* Auther: Piyush Kumar */

    public function students()
    {
        return $this->hasMany(StudentDashboard::class);
    }
}
