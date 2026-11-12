<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usermeta extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'state',
        'state_id',
        'district',
        'district_id',
        'school_id',
        'region_id',
        'school_name',
        'gender',
        'dob',
        'address',
        'pincode',
        'qualification',
        'signature',
        'created_by',
        'module_access'
    ];
    
    public function user()
    {
        return $this->belongsTo(App\Models\User::class, 'user_id');
    }
}
