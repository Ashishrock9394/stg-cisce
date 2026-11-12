<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\School;

class SchoolReference extends Model
{
   use HasFactory;
	protected $table = 'school_reference'; 
	protected $fillable = ['school_user_id','school_id','status'];

	//$timestamps = false;
}
