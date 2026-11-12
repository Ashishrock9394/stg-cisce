<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TermMaster;
use App\Helpers\Helper;
use App\Models\Sstudent;
use App\Models\Sclass;
use App\Models\School;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StudentRecordController extends Controller
{
    public function __construct() {
        $this->middleware('auth.auth_student');
    }   

    public function index() {
        $student = Auth::guard('sstudent')->user();      
        $title = 'Dashboard';
        return view('parent.index', compact('title', 'student'));
    }
    
    public function dashboard()
    {
        $title = 'Student-Dashboard';
        return view('parent.dashboard', compact('title'));
    }

    // Student profile update
    public function viewProfile() {
        $title = "Student Profile";
        $student = Auth::guard('sstudent')->user();
        return view('parent.profile.index', compact('title', 'student'));
    }

    public function updateProfile(Request $request) {

        $request->validate([
            'studentEmail'   => 'required|email|max:255',
            'gender'         => 'required|string|in:Male,Female,TransGender',
            'profilePicture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'mobile'         => 'required|digits:10',
            'apaarId'        => 'nullable|digits:12',
            'domicile'       => 'nullable|string',
            'hobbies'        => 'nullable|string|max:255',
        ], [
            'profilePicture.max' => 'Profile image must be smaller than 2MB.',
        ]);
    
        $student = Auth::guard('sstudent')->user();
        
        try {

            if ($request->hasFile('profilePicture')) {
                $imagePath = Helper::resizeAndSaveImage($request->file('profilePicture'), 100, 'profilePictures/student', $student->id);
                DB::table('students')->where('id', $student->id)->update([
                    'profile_picture' => $imagePath,
                ]);
            }

            $student->update([
                'email_id' => $request->input('studentEmail'),
                'gender'   => $request->input('gender'),
                'apaarId'  => $request->input('apaarId'),
                'domicile' => $request->input('domicile'),
                'mobile'   => $request->input('mobile'),
                'hobbies'  => $request->input('hobbies'),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!'
                ], 200);
            }

            return redirect()->route('student.dashboard')->with('message', 'Profile updated successfully!');

        } catch (\Exception $e) {
            
            Log::error("Profile Update Failed for Student ID {$student->id}: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update profile due to a system error. Please contact support.'
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Server permission issue.');
        }
    }
}