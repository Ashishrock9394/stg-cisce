<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    
    protected $table = 'otp_requests';

    protected $fillable = ['user_id','channel','recipient','otp_hash','attempts','expires_at','verified_at','ip_address','user_agent','created_at','updated_at'];

    protected $dates = ['expires_at'];

    public function isExpired() {

        return $this->expires_at->lt(now());
    }

    public function incrementAttempts() {

        $this->increment('attempts');
    }

}
