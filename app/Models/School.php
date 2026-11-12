<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchoolTrainer;
use App\Models\ScustomClass;
use App\Models\SchoolReference;

class School extends Model
{
    use HasFactory;
	protected $table="schools";
	protected $fillable = ['school_name','school_principal','school_email','city','state','principal_phone',	
	'district','region','address','chain','board','pincode','status','state_id','district_id','region_id','chain_id','registered_by','added_date','zonename'
	];
	public $timestamps = false;
	

	public function getTrainers()
    {
        return $this->hasMany(SchoolTrainer::class,'school_id');
    }

    public function getClasses()
    {
        return $this->hasMany(ScustomClass::class);
    }

    public function getStudents(){
    	return $this->hasMany(Sstudent::class,'school_id');
    }

    public function SchoolTrainer(){
        return $this->hasMany(SchoolTrainer::class,'school_id');
    }

    public function sports() {
        return $this->belongsToMany(Sport::class, 'school_do_sports', 'school_id', 'sports_id');
    }
    
    public function admin() {

        return $this->hasOneThrough(User::class,  SchoolReference::class,'school_id','id','id','school_user_id');
    }
}
