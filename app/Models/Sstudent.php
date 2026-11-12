<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Sstudent extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    protected $table = 'students';
 
    protected $fillable = ['school_id','school_code','student_uid','student_name','gender','class_id','custom_class_id','section_id','dob','user_id','password','password_generated','email_id','mobile','rollno','status','academic_year','is_pwd','profile_picture','batchId','paymentStatus','batchCreatedAt','careersPaymentRef','paidOn','updatedOn','remember_token','created_at','updated_at','domicile','fav_sport','hobbies','aadhaarId','apaarId','passport','program_id','transfer_date'];

	// protected $guard = 'sstudent';

	protected $hidden = ['password','remember_token'];

   public function getAuthIdentifierName() {
		return 'id';
	}

   // Define the password for authentication (date_of_birth)
	public function getAuthPassword() {
	  // return $this->dob;
		// return $this->student_uid;
		return $this->password;
	}

	public function getStudentIdAttribute()
    {
        return $this->school_id . $this->student_uid;
    }


	public function StudentReport()
	{
		return $this->hasMany(Report::class,'student_id');
	}
	
	
	public function classData()
	{
	    return $this->belongsTo(ScustomClass::class, 'custom_class_id');
	}
	
	public function class()
    {
        return $this->belongsTo(CustomClass::class, 'class_id');
    }
	
	
	public function customClass()
    {
        return $this->belongsTo(CustomClass::class, 'custom_class_id');
    }
	
	public function studentInfo()
    {
        return $this->hasOne(StudentInfo::class, 'student_id');
    }

	public function school()
	{
		return $this->belongsTo(School::class, 'school_code', 'school_code');
	}

	
}
