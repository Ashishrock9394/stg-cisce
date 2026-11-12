<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GenerateAdminCredentials extends Command
{
    protected $signature = 'admin:generate-credentials';
    protected $description = 'Generate and update passwords for specific admin users';

    public function handle()
    {
        $userIds = [
            16277, 16278, 16279, 16280, 16281, 16282, 16283, 16284, 16285, 16286,
            16287, 16288, 16289, 16290, 16291, 16292, 16293, 16294, 16295, 16296,
            16297, 16298, 16299, 16300, 16301, 16302, 16303, 16304, 16305, 16306,
            16307, 16308, 16309, 16310, 16311, 16312, 16313, 16314, 16315, 16316,
            16317, 16318, 16319, 16320, 16321, 16322, 16323, 16324, 16325, 16326,
            16327, 16328, 16329, 16330, 16331, 16332, 16333, 16334, 16335, 16336,
            16337, 16338, 16339, 16340, 16341, 16342, 16343, 16344, 16345, 16346,
            16347, 16348, 16349, 16350, 16351, 16352, 16353, 16354, 16355, 16356,
            16357, 16358, 16359, 16360, 16361, 16362, 16363, 16364, 16365, 16366,
            16367
        ];

        $users = DB::table('users')->whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            $this->error('No matching users found.');
            return;
        }

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            $lastFiveDigits = substr(preg_replace('/\D/', '', $user->phone), -5);
            $plainPassword = 'Admin@' . $lastFiveDigits;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($plainPassword)]);

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nPasswords updated successfully for specified users!");
    }
}