<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Enumerable;

class SchoolAdminCredentials implements FromCollection, WithHeadings
{
    protected $schoolUserIds;

    public function __construct(array $schoolUserIds)
    {
        $this->schoolUserIds = $schoolUserIds;
    }

    /**
     * Fetch users + usermeta for export
     */
   public function collection() : Enumerable {

    // Fetch all selected users
    $users = DB::table('users')
        ->whereIn('id', $this->schoolUserIds)
        ->get();

    $data = [];
    

    foreach ($users as $user) {
        // Fetch usermeta (DOB + module_access)
        $usermeta = DB::table('usermetas')->where('user_id', $user->id)->first();

        $status = DB::table('school_reference')->where('school_user_id',$user->id)->value('status');

        $dob = $usermeta->dob ?? null;
        $password = '';
        if($dob) {
            $password = \Carbon\Carbon::parse($dob)->format('Ymd') . '@f365';
        }

        // Fetch assigned modules
        $allowedModules = json_decode($usermeta->module_access ?? '[]', true);
        $assignedModules = DB::table('dashboard_modules')
            ->whereIn('route_name', $allowedModules)
            ->where('role_id', 4)
            ->where('status', 1)
            ->orderBy('id')
            ->pluck('name') // get module names
            ->toArray();

        $assignedModulesStr = implode('  ,  ', $assignedModules); // comma separated

        $data[] = [
            'School Admin ID' => $user->userid ?? '',
            'Name'            => $user->name,
            'Email'           => $user->email,
            'Phone'           => $user->phone,
            'Designation'     => $user->position ?? '',
            'Assigned Modules' => $assignedModulesStr,
            'Status'          => $status == 1 ? 'Active' : 'Inactive',
            'DOB'             => $dob ?? '',
            'UserId'          => $user->email,
            'Password'        => $password,
        ];
    }

    return collect($data);
}



    /**
     * Define headings for Excel
     */
    public function headings(): array
    {
        return [
            'ViewerID',
            'Name',
            'Email',
            'Phone',
            'Designation',
            'Assigned Modules',
            'Status',
            'DOB',
            'UserId',
            'Password'
        ];
    }
}
