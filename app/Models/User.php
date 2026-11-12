<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\School;
use App\Services\Otp\EmailApiService;

use App\Notifications\ResetPasswordNotification;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    
    protected $fillable = [
        'name',
        'email',
		'phone',
		'position',
        'self_registrationId',
        'userid',
        'password',
		'role_id',
        'status',
		'profile_picture',
		'created_at',
		'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_trainers', 'user_id', 'school_id');
    }

    
    // public function trainerRecords()
    // {
    //     return $this->hasMany(TrainerRecord::class, 'user_id');
    // }

    public function usermeta() {
        
        return $this->hasOne(Usermeta::class, 'user_id', 'id');
    }

    
    public function securityAnswers() {
        
        return $this->hasOne(UserSecurityAnswer::class);
    }


    public function sendPasswordResetNotification($token) {

        /**
         * via SMTP 
         * **/ 

        // $this->notify(new ResetPasswordNotification($token));

        /**
         * via Api 
         * **/ 
        app(EmailApiService::class)->sendPasswordResetLink($this, $token);
    }

}
