<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultAdditionalData extends Model
{
    protected $table = 'TestResultAdditionalData';

    protected $primaryKey = 'ResultId';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'ResultId',
        'TimeTaken',
        'MobilityType',
        'PulseCount',
        'AdditionalData',
    ];

    protected $casts = [
        'TimeTaken' => 'decimal:2',
        'MobilityType' => 'integer',
        'PulseCount' => 'integer',
        'AdditionalData' => 'array',
    ];
}