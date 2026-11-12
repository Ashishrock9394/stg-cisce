<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\State;
use App\Models\Usermeta;
use App\Models\Teacher;
use App\Models\Board;
use App\Models\Region;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
use App\Contracts\EmailServiceInterface;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
		
		
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'phone' => ['required', 'string', 'min:10', 'max:10'],
			'state' => ['required'],
			'district' => ['required'],
			'city' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
		
		$chkerro = false;
		$validator = Validator::make([],[]);
		
		if(empty($data['state'])){
			$chkerro = true;
			$validator->errors()->add("The state field is required");
		}
		if(empty($data['district'])){
			$chkerro = true;
			$validator->errors()->add("The district field is required");
		}
		if($chkerro){
			throw new ValidationException($validator);   
		}
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
		
		
		if(!empty($data['state'])){ $state = State::find($data['state']); } 
		if(!empty($data['district'])){ $district = District::find($data['district']); } 
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
			'phone' => $data['phone'],
			'role_id' => 2,
            'password' => Hash::make($data['password']),
        ]);
		
		if($user->id){
            $usermeta = new Usermeta();
            $usermeta->user_id = $user->id;
            if(!empty($state['name'])) $usermeta->state = $state['name'];
			if(!empty($state['id'])) $usermeta->state_id = $state['id'];
            if(!empty($district['name'])) $usermeta->district = $district['name'];
			if(!empty($district['id'])) $usermeta->district_id = $district['id'];
            if(!empty($data['city'])) $usermeta->city = $data['city'];
            
            $usermeta->save();
        }
        return $user;
    }
	 public function getDistrict(Request $request){
        $state_id = $request->id;
        $district_list = District::where('state_id', $state_id)->orderby('name', 'asc')->get();
        $district = '<option value="">Select District</option>';
        if(!empty($district_list)){
            foreach ($district_list as $dist) {
               $district .= '<option value="'.$dist['id'].'">'.$dist['name'].'</option>';
            }
        }
        return $district;

    }



    public function getDistrictList(Request $request) {

        echo "string"; 
        print_r($request->all());
        exit();
        $stateId = $request->post('stateId');
        $cities = District::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json($cities);
    }

    /***
     * Self Registration for schools and trainers 24_Apr_2026
     ***/
    
    public function SelfRegistration(){        
        $title = 'Registration';  
        $states = DB::table('states')->get();
        $districts = DB::table('districts')->get();
        $regions = DB::table('regions')->get();
        return view('auth.self_registration.registration-form', compact('title','states','districts','regions'));
    }
    
    // School Registration 

    public function RegisterSchool(Request $request) {

        $validator = Validator::make($request->all(), [
            'school_code'      => 'required|unique:schools,school_code',
            'school_name'      => 'required|string|max:255',
            'school_email'     => 'required|email|unique:schools,school_email',
            'shift'            => 'required',
            'principal_name'   => 'required|string|max:255',
            'principal_email'  => 'required|email|unique:users,email',
            'principal_mobile' => 'required|numeric|digits:10',
            'position'          => 'required|string|max:255',
            'gender'            => 'required',
            'state'            => 'required',
            'district'            => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors()
            ]);
        }
        
        list($regionName, $regionId) = explode('|', $request->region);
        list($stateId, $stateName) = explode('|', $request->state);
        list($districtId, $districtName) = explode('|', $request->district); 
        $schoolCode = strtoupper($request->school_code);
        $plainPass = $schoolCode . '@f365';
        $password = Hash::make($plainPass);
        $user_id = 'Head.'.$schoolCode.'@cisceschools.org';
        
        // echo"<pre>";print_r($plainPass);exit();
        DB::beginTransaction();

        try {
            $schoolId = DB::table('schools')->insertGetId([
                'school_name'   => $request->school_name,
                'school_code'   => $schoolCode,
                'school_principal' => $request->principal_name,
                'school_email'  => $request->school_email,
                'city'          => $request->city,
                'state'         => $stateName,
                'state_id'      => $stateId,
                'district_id'   => $districtId,
                'district'      => $districtName,
                'region'        => $regionName,
                'shift'         => $request->shift,
                'zonename'     => $request->zone,
                'address'       => $request->address,
                'pincode'       => $request->pincode,
                'status'        => '0',
                'board'         => 'cisce',
                'added_date'    => now(),
            ]);
            $userId = DB::table('users')->insertGetId([
                'name'       => $request->principal_name,
                'email'      => $request->principal_email,
                'userid'     => $user_id,
                'password'   => $password,
                'position'   => $request->position,
                'role_id'    => '4',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Insert into school_users
            DB::table('school_users')->insert([
                'school_id'  => $schoolId,
                'user_id'    => 1,
            ]);

            // 4. Insert into school_reference
            DB::table('school_reference')->insert([
                'school_user_id' => $userId,
                'school_id'      => $schoolId
            ]);

            // 5 isert into usermetas
            DB::table('usermetas')->insert([
                'user_id' => $userId,
                'state' => $stateName,
                'district' => $districtName,
                'state_id' => $stateId,
                'district_id' => $districtId,
                'region_id' =>$regionId,
                'school_id'  => $schoolId,
                'school_name'   => $request->school_name,
                'city' => $request->city,
                'gender' => $request->gender,
            ]);

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'School registered successfully.',
                'credentials' => [
                    'user_id' => $user_id,
                    'password' => $plainPass
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Trainer Registration 

    public function RegisterTrainer(Request $request){
        
        echo"<pre>";print_r($request->all());exit();

        $validator = Validator::make($request->all(), [
            'trainer_name' => 'required|string|max:255',
            'trainer_email' => 'required|email|unique:users,email',
            'trainer_mobile' => 'required|unique:users,phone|digits:10',
            'gender' => 'required|in:Male,Female,Transgender',
            'qualification' => 'required|string|max:255',
            'state' => 'required|integer|exists:states,id',
            'district' => 'required|integer|exists:districts,id',
            'city' => 'required|string|max:255',
            'block' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'marketing_consent' => 'accepted',
            'privacy_policy' => 'accepted',
            'term_condition' => 'accepted',
            'trainer_dob' => ['required', 'date', 'after_or_equal:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $dob = \Carbon\Carbon::parse($value);  $age = $dob->age;

                    if ($request->has('term_condition') && $age < 18) {
                        $fail('You must be at least 18 years old to accept the Terms and Conditions.');
                    }
                }],
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        list($stateId, $stateName) = explode('|', $request->state);
        list($districtId, $districtName) = explode('|', $request->district); 

        $dob = $request->post('dob');
        $password = str_replace('-', '', $dob);

        $teacher = Teacher::create([
            'name' => $request->trainerName,
            'email' => $request->email,
            'phone' => $request->mobile,
            'position' => 'Trainer',
            'role_id' => 3,
            'status'  => 1,
            'password' => Hash::make($password),
        ]);

        $school_id = DB::table('school_reference')->where('school_user_id', auth()->id())->value('school_id');
        
        if($teacher){ 
            $usermeta = new Usermeta();
            $usermeta->user_id = $teacher->id;
            $usermeta->state = $state->name;
            $usermeta->state_id = $state->id;
            $usermeta->district = $district->name;
            $usermeta->district_id = $district->id;
            $usermeta->city = $request->city;
            $usermeta->school_name = null;
            $usermeta->school_id = $school_id;
            $usermeta->created_by = Auth::user()->id ?? null;
            $usermeta->class = null;
            $usermeta->subject = null;
            $usermeta->qualification = $request->qualification;
            $usermeta->dob = $request->dob;
            $usermeta->achievement = $request->achievement ?? null;
            $usermeta->gender = $request->gender;    
            $usermeta->save();
        }
        
	    $exists = DB::table('school_trainers')->where('trainer_id', $request->trainer_id)->where('school_id', $school_id)->exists();
	    if ($exists) {
	        return response()->json(['message' => 'Trainer is already mapped to this school.'], 200);
	    }
        
	    DB::table('school_trainers')->insert([
	        'trainer_id' => $teacher->id,
	        'school_id' => $school_id,
	        'status' => 0,
	        'created_at' => now(),
	        'updated_at' => now(),
	        'mapped_on'  => now(),
	    ]);

        $teacher->save();
        $serial = str_pad($teacher->id, 7, '0', STR_PAD_LEFT);
        $self_registrationId = 'GFFT' . $serial;
        $teacher->update(['self_registrationId' => $self_registrationId]);

        return back()->with('success', 'Trainer saved successfully!');
    }




}
