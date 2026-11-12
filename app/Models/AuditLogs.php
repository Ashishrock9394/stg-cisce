<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model {

    use HasFactory;
	protected $table = 'cisce_audit_logs';
    protected $fillable = ['user_id','action_type','description','ip_address','user_agent'];
		
	
}
