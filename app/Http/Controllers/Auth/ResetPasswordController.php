<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UsersBackupCode;
use App\Models\User;
use App\Notifications\SendBackupCodeNotification;

use App\Services\Otp\EmailApiService;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login' ; //RouteServiceProvider::HOME;



    /* Override The Defualt Reset Password's Response  */
    protected function resetPassword($user, $password) {

        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        //DB::table('sessions')->where('user_id', $user->id)->delete();   //need to change the session driver to database driver for centralised
        Auth::logout();
    }
    
    protected function sendResetResponse(Request $request, $response) {

        $user = User::where('email', $request->email)->firstOrFail();
        $backupCode = strtoupper(bin2hex(random_bytes(4)));

        UsersBackupCode::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash' => Hash::make($backupCode),
                'used'      => 0,
                'updated_at' => now(),
            ]
        );

        /**
         * via smtp 
         * **/ 
        // $user->notify(new SendBackupCodeNotification($backupCode, $user));
        
        /**
         * via Api 
         * **/ 
        app(EmailApiService::class)->sendBackupCode($user, $backupCode);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'backup_code' => $backupCode,
                'message' => __('Your password has been reset successfully.')
            ]);
        }

        return redirect($this->redirectPath())->with('status', __($response));
    }

    protected function sendResetFailedResponse(Request $request, $response) {

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => __($response)
            ], 422);
        }

        return back()->withErrors(['email' => __($response)]);
    }


}
