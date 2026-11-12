<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersBackupCode extends Model
{
    use HasFactory;

    protected $table = 'user_backup_codes';
    protected $fillable = ['user_id','code_hash','used'];

    public $timestamps = true;
}
