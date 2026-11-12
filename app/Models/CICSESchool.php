<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sstudent;

class CICSESchool extends Model
{
    use HasFactory;

    protected $table = "schools";

    protected $fillable = ['school_name','school_code', 'zonename', 'region',  'school_principal','school_email','city','state','principal_phone',  'district','region','address','chain','board','pincode','status','state_id','district_id','region_id','chain_id','registered_by','added_date','zonename','shift' ];

    public $timestamps = false;


    public function students() {

        return $this->hasMany(Sstudent::class, 'school_id');
    }
}
