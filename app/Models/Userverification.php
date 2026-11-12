<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Userverification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
      'user_id', 'email', 'phone', 'otp', 'isverified'
    ];

    public function user()
    {
        return $this->belongsTo(App\Models\User::class, 'user_id');
    }
    
}