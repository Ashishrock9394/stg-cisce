<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveSchool extends Model
{
    use HasFactory;
	protected $table = 'active_school';
    protected $fillable = ['school_id','school_user_id','school_code','status'];
		
	    
}
