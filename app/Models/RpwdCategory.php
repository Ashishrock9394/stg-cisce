<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpwdCategory extends Model
{
    use HasFactory;

    protected $table = 'pwd_categories';


    public function mappings()
    {
        return $this->hasMany(PwdCategoryTestMapping::class, 'pwd_category_id', 'id');
    }
}
