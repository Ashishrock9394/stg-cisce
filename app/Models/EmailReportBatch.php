<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailReportBatch extends Model
{
    
	use HasFactory;
	protected $table ='email_report_batches';
	protected $fillable = ['school_id','total_students','completed_students','status'];

	public $timestamps = true;
}
