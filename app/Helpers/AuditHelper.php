<?php 


namespace App\Helpers;


use App\Models\CisceAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AuditHelper
{
    public static function log($actionType, $description = null)
    {
        if (Auth::check()) {
            CisceAuditLog::create([
                'user_id'   => Auth::id(),
                'action_type' => $actionType,
                'ip_address'  => Crypt::encryptString(request()->ip()),
                'user_agent'  => request()->header('User-Agent'),
                'description' => $description,
            ]);
        }
    }
}
