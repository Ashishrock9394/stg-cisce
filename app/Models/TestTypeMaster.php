<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestTypeMaster extends Model
{
    use HasFactory;
    protected $table = 'TestTypeMaster';

    protected $primaryKey = 'TestTypeID';
}
