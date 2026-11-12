<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestImportLog extends Model
{
    use HasFactory;

    protected $table = 'test_import_logs';

    protected $fillable = [
        'id',
        'school_id',
        'user_id',
        'skill_report_id',
        'file_path',
        'filename',
        'status',        // pending, processing, completed, failed
        'message',
        'error_file',    // path to error file if import failed
        'started_at',
        'completed_at',  // timestamp when processing completed
        'is_active'
    ];

    protected $dates = ['completed_at'];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

}
