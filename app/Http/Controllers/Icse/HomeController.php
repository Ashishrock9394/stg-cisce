<?php

namespace App\Http\Controllers\Icse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendNotificationEmailJob;
use DB;
use Session;
use App\Services\AcsEmailService;

class HomeController extends Controller
{

    /**
     * STATIC PAGE FOR CISCE HOME PAGE 
     * */
    protected $emailService;

    // public function __construct(AcsEmailService $emailService) {
    //     $this->emailService = $emailService;
    // }

    public function TrainingResources() {



        if (!Auth::check()) {
            return redirect('/login');
        }
        
        $title = 'Training Resources';

        $TrainingKits = DB::table('trainings_videos')->where('status','active')
            ->orderBy('id', 'asc')
            ->get();

        return view('homepages.index', compact('title','TrainingKits' ));
    }


    public function TrainingVideos() {

        $title = 'Training Videos';

        $TrainingKits = DB::table('trainings_videos')->where('status','active')
            ->orderBy('id', 'asc')
            ->get();

        return view('homepages.training', compact('title','TrainingKits' ));
    }

    public function BatteriesOfTests(){
        
        $title = __FUNCTION__;

        $videoData = DB::table('fitness_test_videos')
            ->join('skill_reports', 'skill_reports.TestTypeMasterID', '=', 'fitness_test_videos.testType_id')
            ->select(
                'skill_reports.id as skill_id',
                'skill_reports.skill_name',
                'skill_reports.icons',
                'fitness_test_videos.testType_id',
                'fitness_test_videos.type_video',
                'fitness_test_videos.video_url'
            )
            ->get();

        foreach ($videoData as $video) {
            if ($video->skill_id == 18) {
                $video->skill_name = "Body Mass Index (BMI)";
            }
        }

        $groupedVideos = $videoData->groupBy('skill_id');

        $juniorVideos = collect();
        $seniorVideos = collect();
        

        foreach ($groupedVideos as $skillId => $videos) {
            $icons = $videos->pluck('icons')->unique()->toArray();
            if ($skillId <= 18) {
                $juniorVideos->push([
                    'skill_id' => $skillId,
                    'videos' => $videos,
                    'icons' => $icons
                ]);
            }
            if($skillId >= 18 ){
                $seniorVideos->push([
                    'skill_id' => $skillId,
                    'videos' => $videos,
                    'icons' => $icons 
                ]);
            }
        }
        // dd($juniorVideos);

        return view('homepages.batteries-of-tests', compact('title', 'juniorVideos', 'seniorVideos', 'videoData'));
    }


    public function TrainingManual(){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $filePath = storage_path('app/public/staticpages/FitnessAssessmentProgrammeManual.pdf');
        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TrainingManual.pdf"',
        ]);
    }

    public function Sop(){

        echo "string"; exit();

        $filePath = storage_path('app/public/staticpages/SOP.pdf'); 

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="sample.pdf"',
        ]);
    }

    public function UploadData(){

        $title = 'Student Data Not Found';
        return view('homepages.no-student-data', compact('title'));
    }

    public function DemoVideos(){

        $title = __function__;
        return view('homepages.demo-videos', compact('title'));
    }

    public function ContactUs(){
        $title = 'Contact';
        return view('homepages.contact-us', compact('title'));
    }


    public function Faq(){

        $title = 'FAQ';
        if (!Auth::check()) {
            return view('homepages.faq', compact('title'));
        }
        return view('homepages.adminfaq', compact('title'));
    }


    /** Only for logged in users **/
    public function ContactMe(){

        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userId = Auth::user()->id;
            $role_id = Auth::user()->role_id;

            if ($role_id == 3) {

                if (Session::get('SelectSchoolId')) {
                    $schoolId = Session::get('SelectSchoolId');
                } else {
                    $schoolId = DB::table('school_trainers')
                        ->join('schools', 'schools.id', '=', 'school_trainers.school_id')
                        ->where('school_trainers.trainer_id', $userId)
                        ->where('school_trainers.status', 1)
                        ->value('school_trainers.school_id');
                }

            } else {
                $schoolId = DB::table('school_reference')
                    ->where('school_user_id', $userId)
                    ->where('status', 1)
                    ->value('school_id');
            }

            $school = DB::table('schools')
                ->where('id', $schoolId)
                ->first();

            if (!$school) {
                return redirect()->back()->with('error', 'School not found.');
            }

            $contacts = DB::table('zonal_sports_coordinator as zsc')
                ->leftJoin('regions', 'zsc.region_id', '=', 'regions.id')
                ->leftJoin('zones', 'zsc.zone_id', '=', 'zones.id')
                ->select(
                    'zsc.name',
                    'zsc.email',
                    'zsc.phone',
                    'zsc.role_id',
                    'zsc.school_code',
                    'regions.name as region_name',
                    'zones.name as zone_name'
                )
                ->where('regions.name', $school->region)
                ->where(function ($q) use ($school) {
                    $q->where('zsc.role_id', 5)
                        ->orWhere(function ($sub) use ($school) {
                            $sub->where('zsc.role_id', 6)
                                ->where('zones.name', $school->zonename);
                        });
                })
                ->get();

            $title = 'Contact Us';

            return view('homepages.contact-me', compact('title', 'contacts'));

        } catch (\Throwable $e) {

            \Log::error('ContactMe Error', [
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with(
                'error',
                'Something went wrong while loading the contact details. Please try again.'
            );
        }
    }

    public function privacyPolicy(){
        $title = 'Privacy Policy';
        return view('homepages.privacy-policy', compact('title'));
    }

    public function TermsConditions(){
        $title = "Terms & Conditions";
        return view('homepages.terms-conditions', compact('title'));
    }
	
	
	public function azureFrom() 
	{
		
		// Mail::raw('Dear Sir, <br>We have implemented azure mail service. <br>please have a look.<br> regards<br> Bhawani', function ($message) {
		// 	$message->to('sujit@seqfast.com')
		// 	->to('bhawani@seqfast.com')
		// 	->to('amit.kumar@seqfast.com')
		// 	->to('ashish@seqfast.com')
		// 	->subject('Azure mail service');
		// });
		
		
		#die('----change the detail of payment detail---');
		
		$title = 'Azure Form';
		return view('homepages.azure-upload', compact('title'));
	}
	
	
	
	public function uploadToAzure(Request $request)
	{
		// 1. Validate the file (max 10MB in this example)
		$request->validate([
			'my_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
		]);

		if ($request->hasFile('my_image')) 
		{
			$file = $request->file('my_image');
			
			// 2. Create a unique name or use the original
			$fileName = time() . '_' . $file->getClientOriginalName();
			
			// 3. Define the path (Folder + Filename)
			$fullPath = 'excel_storage/' . $fileName;

			// 4. Upload using the 'azure' disk we configured
			// We use putFileAs because it is memory-efficient (Streaming)
			$uploadedPath = Storage::disk('azure')->putFileAs(
				'excel_storage', 
				$file, 
				$fileName
			);

			if ($uploadedPath) 
			{
				return back()->with('success', 'Image uploaded to Azure successfully! Path: ' . $uploadedPath);
			}
		}

		return back()->with('error', 'Upload failed.');
	}

    public function moveFilesToAzure(){
        try {

            $folder = 'test_errors';

            $files = Storage::disk('local')->allFiles($folder);

            if (empty($files)) {
                Log::info('No files found in test_errors folder.');
                return redirect('/');
            }

            foreach ($files as $filePath) {

                $stream = Storage::disk('local')->readStream($filePath);

                if (!$stream) {
                    Log::warning("Unable to read file: {$filePath}");
                    continue;
                }

                Storage::disk('azure')->writeStream($filePath, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                if (Storage::disk('azure')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                    Log::info("File moved successfully: {$filePath}");
                } else {
                    Log::error("Failed to upload file to Azure: {$filePath}");
                }
            }

            Log::info('All files uploaded to Azure test_errors folder successfully.');
            return redirect('/');

        } catch (\Exception $e) {

            Log::error('Migration failed.', [
                'error' => $e->getMessage()
            ]);
            return redirect('/');
        }
    }

  

    public function sendNotificationView(){
        if (!Auth::check()) {
            return redirect('/login');
        }

        $title = "Send Email Notification";

        return view('homepages.email-notification', compact('title'));
    }

    public function sendNotificationEmail(Request $request){
        try{
            
            $request->validate([
                'user_type' => 'required',
                'send_to'  => 'required',
                'count'    => 'nullable|integer',
                'subject'  => 'required|string',
                'message'  => 'required|string',
                'attachment' => 'nullable|file|max:512',
            ]);

            
            // echo"<pre>";print_r($request->all());exit();            

            if($request->user_type == '4'){
                $users = DB::table('school_reference')
                ->join('users', 'users.id', '=', 'school_reference.school_user_id')
                ->join('schools', 'schools.id', '=', 'school_reference.school_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.notification_sent',
                    'schools.school_name',
                    'schools.school_code',
                )
                // ->where('users.notification_sent', '0');  
                ->whereIn('users.id', [3251,3139]); 
                // echo"<pre>";print_r($users->get());exit();
                
            }else{
                $users = DB::table('school_trainers as st')
                ->join(DB::raw("
                    (
                        SELECT MIN(id) as id
                        FROM school_trainers
                        WHERE status = '1'
                        GROUP BY trainer_id
                    ) as first_st
                "), 'st.id', '=', 'first_st.id')
                ->join('users', 'users.id', '=', 'st.trainer_id')
                ->join('schools', 'schools.id', '=', 'st.school_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'schools.school_name',
                    'schools.school_code'
                )
                ->whereIn('users.id', [4065,5931]); 
                
                // ->where('users.notification_sent', '0');
            }

            if ($request->send_to != 'all') {
                $users->limit($request->count);
            }

            $users = $users->get();

            $attachmentPath = null;

            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('notification_attachments', 'public');
            }

            $message = nl2br($request->message);

            
            echo"<pre>";print_r($message);exit();

            foreach ($users as $user) {

                SendNotificationEmailJob::dispatch(
                    $user,
                    $request->subject,
                    $message,
                    $attachmentPath
                )->onQueue('import_emails');

                DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'notification_sent' => 1
                ]);
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Emails have been queued successfully.',
            ]);

        }catch (\Throwable $e) {

            Log::error('Failed to queue notification emails.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Unable to queue notification emails.',
                'error'   => $e->getMessage(),
            ], 500);
        }

    }

}
