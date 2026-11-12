<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermMaster extends Model
{
    protected $table = 'term_masters';
    protected $fillable = ['school_id','term_name','camp_type','academic_year','term_start_date','term_end_date','is_active','is_deleted','created_at','updated_at'];
    use HasFactory;
}
