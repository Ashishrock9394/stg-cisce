<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePrivilage extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_id',
		'pri_id',
		'ent_id'
    ];
	
	public $timestamps = false;
}
