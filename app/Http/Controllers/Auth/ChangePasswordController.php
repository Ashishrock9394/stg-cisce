<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SecurityQuestion;
use App\Models\UserSecurityAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\UsersBackupCode;
use DB;
use App\Models\User;
use App\Notifications\SendBackupCodeNotification;
use App\Services\Otp\EmailApiService;

class ChangePasswordController extends Controller
{

    // Show the form
    public function showChangeForm() {

        /* businees logic*/
        $SecurityQuestions = SecurityQuestion::where('status','active')->get();

        $user = Auth::user()->load([
            'securityAnswers.firstQuestion:id,question',
            'securityAnswers.secondQuestion:id,question',
        ]);

        $isSet = true;
        if (!$user || !$user->securityAnswers) {
            $isSet = false;
        }

        $title = 'Change Password';
        return view('auth.passwords.change', compact('title', 'SecurityQuestions', 'isSet'));
    }


    public function updateQuestions(Request $request) {

        $validator = Validator::make($request->all(), [
            'question1' => 'required|string|max:255',
            'answer1' => 'required|string|max:255',
            'question2' => 'required|string|max:255',
            'answer2' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $userId = Auth::id();
            
            UserSecurityAnswer::updateOrCreate(
                [ 'user_id'    => $userId ],
                [
                    'question_1'=> $request->question1,
                    'question_2'=> $request->question2,
                    'answer_1_hash'     => Hash::make($request->answer1),
                    'answer_2_hash'     => Hash::make($request->answer2)
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Security questions updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update security questions: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updatePassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [ 'required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

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
             * via SMTP 
             * **/ 
            // $user->notify(new SendBackupCodeNotification($backupCode, $user));

            /**
             * via Api 
             * **/ 
            app(EmailApiService::class)->sendBackupCode($user, $backupCode);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!',
                'backup_code' => $backupCode
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }



    // Handle update
    public function update(Request $request) {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if current password matches
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        // Update password
        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password successfully changed!');
    }
}
