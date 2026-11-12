<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportBatch extends Model
{
    
	use HasFactory;
	protected $table ='report_batches';
	protected $fillable = ['school_id','total_students','completed_students', 'final_zip_path' ,'status','download_path','expires_at'];

	public $timestamps = true;
}
