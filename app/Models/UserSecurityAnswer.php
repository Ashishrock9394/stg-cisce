<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSecurityAnswer extends Model
{
    use HasFactory;
    
    protected $table = 'user_security_answers';

    protected $fillable = [ 'user_id', 'question_1', 'answer_1_hash','question_2','answer_2_hash'];
    protected $hidden = [ 'answer_1_hash', 'answer_2_hash'];

    public $timestamps = true;


    public function user() {

        return $this->belongsTo(User::class);
    }

    public function firstQuestion() {

        return $this->belongsTo(SecurityQuestion::class, 'question_1');
    }

    public function secondQuestion() {
        
        return $this->belongsTo(SecurityQuestion::class, 'question_2');
    }


}
