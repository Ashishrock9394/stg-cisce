<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillReportSkillTypeTermTypeMapping extends Model
{
    use HasFactory;
	
	 protected $table = 'skillreport_skilltype_termtype_mapping';

    // Allow mass assignment for all columns
    protected $fillable = [
		'school_id',
        'skill_report_id',
        'skill_type_id',
        'term_type_id',
		'skill_type_value2',
		'term_type2_id','created_by'
    ];
}
