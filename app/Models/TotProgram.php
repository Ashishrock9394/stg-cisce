<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TotProgram extends Model
{
    use HasFactory;

     protected $fillable = [
        'name',
        'location',
        'start_date',
        'end_date',
        'status',
    ];



    public function trainerMappings() {
        return $this->hasMany(TotTrainerMapping::class);
    }

    public function trainers() {

        return $this->hasManyThrough(User::class, TotTrainerMapping::class,
            'tot_program_id', // Foreign key on TotTrainerMapping table
            'id',             // Foreign key on User table
            'id',             // Local key on TotProgram
            'trainer_id'      // Local key on TotTrainerMapping
        );
    }


}
