<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ScustomClass extends Model
{
   use HasFactory;
	protected $table = 'custom_classes';
   protected $fillable = ['school_id','class_id','section','orders','status','nomenclature'];
	public $timestamps = false;
	
	


	public function reports()
   {
       return $this->hasMany(Report::class, 'custom_class_id');
   }

   public function school() {
      
      return $this->belongsTo(School::class, 'school_id'); 
   }

	public function class()  {
		return $this->belongsTo(Sclass::class, 'class_id');
	}

	
}
