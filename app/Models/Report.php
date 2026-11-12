<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
   use HasFactory;
	protected $table = 'reports';
    #protected $fillable = ['name'];
	public $timestamps = false;
	

/*	public function student()
	{
	  return $this->belongsTo(StudentDashboard::class::class, 'student_id');
	}
*/	
	
	/*
	auther: need to change 
	*/
	public function student()
    {
        return $this->belongsTo(StudentDashboard::class, 'student_id', 'id');
    }

	public function trainer()
	{
	  return $this->belongsTo(User::class, 'submitted_by');
	}

	public function level()
	{
	  return $this->belongsTo(Level::class, 'level');
	}

	
	public function customClass() {

      return $this->belongsTo(ScustomClass::class, 'custom_class_id');
   }

	public function class() {

      return $this->belongsTo(Sclass::class, 'id');
   }

   public function classname()  {
		return $this->belongsTo(Sclass::class,'class_id');
	}

   public function skillArea() {

      return $this->belongsTo(Skill::class,'skill_area_id');
   }

   public function sport() {
      return $this->belongsTo(Sport::class,'skill_sports_id');
   }

   public function technique(){
   	return $this->belongsTo(Technique::class,'technique_id');
   }

   public function activity(){
   	return $this->belongsTo(Activity::class,'activity_id');
   }
   

   public function getLevel(){
   	return $this->hasOne(Level::class,'id');
   }
}
