<?php
// app/Models/TotTrainerMapping.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TotTrainerMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'tot_program_id',
        'trainer_id',
        'tot_school_id',
        'original_school_data',
        'status',
        'mapped_on',
        'restored_on'
    ];

    protected $casts = [
        'original_school_data' => 'array',
        'mapped_on' => 'datetime',
        'restored_on' => 'datetime'
    ];

    // Relationships
    public function totProgram()
    {
        return $this->belongsTo(TotProgram::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function totSchool()
    {
        return $this->belongsTo(School::class, 'tot_school_id');
    }

    // Accessors
    public function getOriginalSchoolsAttribute(): Collection
    {
        if (!$this->original_school_data) {
            return collect();
        }
        
        return collect($this->original_school_data);
    }

    public function getFirstOriginalSchoolIdAttribute()
    {
        return $this->original_schools->first()['school_id'] ?? null;
    }

    public function getActiveOriginalSchoolsAttribute(): Collection
    {
        return $this->original_schools->where('original_status', 1);
    }

    public function getHadActiveMappingsAttribute(): bool
    {
        return $this->active_original_schools->isNotEmpty();
    }

    // Business logic methods
    public function isNewTrainer(): bool
    {
        return $this->status === 'new';
    }

    public function isMappedTrainer(): bool
    {
        return $this->status === 'mapped';
    }

    public function isRestored(): bool
    {
        return !is_null($this->restored_on);
    }

    public function markAsRestored(): bool
    {
        return $this->update([
            'status' => 'restored',
            'restored_on' => now()
        ]);
    }

    // Scope methods
    public function scopeForProgram($query, $programId)
    {
        return $query->where('tot_program_id', $programId);
    }

    public function scopeNotRestored($query)
    {
        return $query->whereNull('restored_on');
    }

    public function scopeMappedTrainers($query)
    {
        return $query->where('status', 'mapped');
    }

    public function scopeNewTrainers($query)
    {
        return $query->where('status', 'new');
    }
}