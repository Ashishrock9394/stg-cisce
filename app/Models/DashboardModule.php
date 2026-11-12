<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardModule extends Model
{
    
	use HasFactory;
	protected $table ='dashboard_modules';
	protected $fillable = ['role_id','name','slug','route_name','icon','order_no','status'];

	public $timestamps = true;
}
