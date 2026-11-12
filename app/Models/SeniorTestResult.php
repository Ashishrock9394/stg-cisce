<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeniorTestResult extends Model
{
   use HasFactory;
	protected $table = 'SeniorTestResults';
   
   protected $primaryKey = 'ResultId';

	public $incrementing = true;

	protected $keyType = 'int';
 
}
