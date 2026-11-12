<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\School;

class SchoolUsers extends Model
{
   use HasFactory;

	protected $table = 'school_users';

	protected $fillable = ['user_id','school_id','status'];
	
	public $timestamps = false;

	public function getSchoolList(){
		return $this->hasMany(CICSESchool::class,'id', 'school_id');
	}
}
