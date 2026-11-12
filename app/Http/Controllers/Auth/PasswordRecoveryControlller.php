<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password;
// use Illuminate\Support\Str;

use App\Models\User;
use App\Models\UsersBackupCode;

class PasswordRecoveryControlller extends Controller
{
 
    /* check the existance of users */

    public function checkEmailExists(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'exists' => false,
                'message' => 'Invalid email format'
            ]);
        }

        $exists = User::where('email', $request->email)->exists();
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email exists' : 'No account found with this email'
        ]);
    }


    public function verifySecurityCode(Request $request) {

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'security_code' => ['required', 'string'],
        ], [
            'email.exists' => 'No account found with this email address',
            'security_code.required' => 'Security code is required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            $backupCode = UsersBackupCode::where('user_id', $user->id)
                ->where('used', 0)
                ->latest()
                ->first();

            if (!$backupCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active security code found'
                ], 422);
            }

            if (!Hash::check($request->security_code, $backupCode->code_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid security code'
                ], 422);
            }

            $backupCode->update(['used' => 1]);

            $token = Password::broker()->createToken($user);
            return response()->json([
                'success' => true,
                'message' => 'Security code verified successfully',
                'email' => $user->email,
                'token' => $token
            ]);
        

        } catch (\Throwable $e) {

            \Log::error('Backup code verification error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify security code'
            ], 500);
        }
    }


    public function getUserSecurityQuestions(Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->email)
                ->with([
                    'securityAnswers.firstQuestion:id,question',
                    'securityAnswers.secondQuestion:id,question',
                ])
                ->first();

            if (!$user || !$user->securityAnswers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Security questions not set for this account',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'questions' => [
                    [
                        'id'   => $user->securityAnswers->firstQuestion->id,
                        'question1' => $user->securityAnswers->firstQuestion->question,
                    ],
                    [
                        'id'   => $user->securityAnswers->secondQuestion->id,
                        'question2' => $user->securityAnswers->secondQuestion->question,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            \Log::error('Get security questions error', [
                'email' => $request->email,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve security questions',
            ], 500);
        }
    }


    public function verifySecurityQuestions(Request $request) {
       
        $validator = Validator::make($request->all(), [
            'email'   => 'required|email',
            'answer1' => 'required|string',
            'answer2' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

       
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

       
        $answer1 = strtolower(trim($request->answer1));
        $answer2 = strtolower(trim($request->answer2));
        $securityAnswers = $user->securityAnswers;

        $isAnswer1Valid = Hash::check($request->answer1, $securityAnswers->answer_1_hash);
        $isAnswer2Valid = Hash::check($request->answer2, $securityAnswers->answer_2_hash);


        
        if ($isAnswer1Valid && $isAnswer2Valid) {
            $token = Password::broker()->createToken($user);
            return response()->json([
                'success' => true,
                'message' => 'Security questions verified successfully',
                'email' => $user->email,
                'token' => $token                
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Security answers are incorrect'
        ], 422);
    }





}
