<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedStudent extends Model
{
    use HasFactory;

    protected $fillable = ['student_id','name','student_uid','school_id','custom_class_id','deleted_by','deleted_at','json_data','restored','restoration_date'];

    protected $casts = [
        'json_data' => 'array',
        'deleted_at' => 'datetime',
    ];
    public $timestamps = false;


}
