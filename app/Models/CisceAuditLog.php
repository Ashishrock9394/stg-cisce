<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CisceAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'action_type', 'record_count', 'ip_address', 'user_agent', 'description'];
}
