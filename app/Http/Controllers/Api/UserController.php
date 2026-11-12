<?php
namespace App\Http\Controllers\Api;
use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use App\Models\User;
use App\Models\Usermeta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use ErrorException;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;

class UserController extends Controller
{	
	public function __construct(){
        $this->middleware('auth:api', ['except' => ['login', 'store', 'check']]);
    }
	
	private $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher 
    private $CIPHER_KEY_LEN = 16; //128 bits

   
 
   function encrypt($key, $iv, $data) {
        if (strlen($key) < $this->CIPHER_KEY_LEN) {
            $key = str_pad("$key", $this->CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $this->CIPHER_KEY_LEN) {
            $key = substr($str, 0, $this->CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, $this->OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData;
        
        return $encryptedPayload;
        
    }
   
   
    function decrypt($key, $iv, $data) {
        if (strlen($key) < $this->CIPHER_KEY_LEN) {
            $key = str_pad("$key", $this->CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $this->CIPHER_KEY_LEN) {
            $key = substr($str, 0, $this->CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        
       // $parts = explode(':', $data); 
        //$decryptedData = openssl_decrypt(base64_decode($parts[0]), $this->OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
		
		
        $decryptedData = openssl_decrypt( base64_decode($data), $this->OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedData;
    }
	
	
	function check(Request $request){		
		//dd($request);		
		$email = $request->email;
		
		if(is_numeric($email)){
	
			$validator = Validator::make(
				array("phone" => $email), 
				['phone' => 'required|digits:10']
			);
			
		} else if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			
			$validator = Validator::make(array("email" => $email),[
				'email' => 'required|email',				
			]);
			
		} else{
			return Response::json(array(
				'status' => 'error', 'code'  =>  422, 'message'   =>  'Invalid Input'
			), 422);
		}		
		
		/*$validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);*/
		
		if($validator->fails()){
            return Response::json(array(
                'status'    => 'error',
                'code'      =>  422,
                'message'   =>  $validator->messages()->first()
            ), 422);
        }		
		
		if(is_numeric($email)){
	
		    $user = User::where('phone', $request->email)->first();
			
		} else if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
			
			$user = User::where('email', $request->email)->first();				
		}
		
		
		 //$user = User::where('email', $request->email)->first();
		 
		 if($user){			
			return Response::json(array(
                'status'    => 'success',
                'code'      =>  200,
                'message'   =>  'User exist with  '.$user->email
            ), 200);
		 }
		
		return Response::json(array(
                'status'    => 'error',
                'code'      =>  422,
                'message'   =>  'User not found'
            ), 422);
	}
	
	
	function login(Request $request){
		
		try{ 
			$iv = "fedcba9876543210"; #Same as in JAVA
			$key = "0a9b8c7d6e5f4g3h"; #Same as in JAVA
			
			$data = $request->email;
		//	$encrypted = $this->encrypt($key, $iv, $data);

		
			if (strpos($request->email, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid email'
				), 422);
			}
			
			if (strpos($request->password, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid password'
				), 422);
			}
			
			if (strpos($request->reqtime, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid request'
				), 422);
			}
			
	/*
			if (strpos($request->rcaptcha, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid request'
				), 422);
			}
			
			if(empty($request->captcha)) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Captcha Required'
				), 422);
			}
			
			*/
			
			$reqtimevar = $this->decrypt($key, $iv, $request->reqtime);
			
			//$rcaptchavar = $this->decrypt($key, $iv, $request->rcaptcha);
			/*
			if($request->captcha != $rcaptchavar) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Invalid Captcha'
				), 422);
			}
			*/
			
			
			$key = $reqtimevar . 'fitind';
			//echo $data."<br>";
			//$encrypted = $this->encrypt($key, $iv, $data);
			//return $encrypted;
			
			$email = $this->decrypt($key, $iv, $request->email);
			$password = $this->decrypt($key, $iv, $request->password);
			//return $email;
			
			if(is_numeric($email)){
				
				$validator = Validator::make(
					array( "phone" => $email, "password" => $password), 
					['phone' => 'required|digits:10', 'password' => 'required|string|min:6']
				);
				
			}else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
				$validator = Validator::make( array( "email" => $email, "password" => $password ), [
					'email' => 'required|email',
					'password' => 'required|string|min:6',
				]);
				
			}else{
				return Response::json(array(
					'status' => 'error', 'code'  =>  422, 'message'   =>  'Invalid Input'
				), 422);
			}
			
			
			/*

			$validator = Validator::make(array("email"=>$email, "password"=>$password), [
				'email' => 'required|email',
				'password' => 'required|string|min:6',
			]);
			*/

			if ($validator->fails()) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  $validator->messages()->first()
				), 422);
			}		
		
			
			if (! $token = auth('api')->attempt($validator->validated())) {
					return Response::json(array(
						'status' => 'error','code' => 401, 'message' =>  'Unauthorized'
					), 401);
				}


			return Response::json(array(
				'token' => $this->createNewToken($token),
				'status'    => 'success',
				'code'      =>  200,
				'reqtime' => $request->reqtime,
				'message'   =>  array('msg'=>'You are successfully logged in')
			), 200);
			
			
		} catch(Exception $e){ 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
		}
	}	
	
	public function getAuthUser(Request $request)
    {
        return response()->json(auth('api')->user());
    }	
    
    function store(Request $request){
		try{ 
			$iv = "fedcba9876543210"; #Same as in JAVA
			$key = "0a9b8c7d6e5f4g3h"; #Same as in JAVA
			
		if (strpos($request->email, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid email'
				), 422);
			}
			
			if (strpos($request->password, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid password'
				), 422);
			}
			
			if (strpos($request->reqtime, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid request'
				), 422);
			}
			
			/*
			if (strpos($request->rcaptcha, '=') == false) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Not valid request'
				), 422);
			}
			
			if(empty($request->captcha)) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Captcha Required'
				), 422);
			}
			*/
			
			//$email = $this->decrypt($key, $request->email);
			//$password = $this->decrypt($key, $request->password);
			
			$reqtimevar = $this->decrypt($key, $iv, $request->reqtime);
			//$rcaptchavar = $this->decrypt($key, $iv, $request->rcaptcha);
			/*
			if($request->captcha != $rcaptchavar) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  'Invalid Captcha'
				), 422);
			}
			*/
			
			
			$key = $reqtimevar . 'fitind';
			
			$email = $this->decrypt($key, $iv, $request->email);
			$password = $this->decrypt($key, $iv, $request->password);
			
			
			
			$rules=array(
				'name' => ['required', 'string', 'max:255'],
				'role' => ['required', 'in:subscriber,school,group' ],
				'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
				'phone' => ['required', 'string', 'min:10', 'max:10'],
				'password' => ['required', 'string', 'min:8'],
				//'age' => ['required', 'min:1','max:2' ],
			);
		   
		   
		   
		   $validator = Validator::make( array( "email" => $email, "password" => $password, "role"=> $request->role , "name" => $request->name, "phone" => $request->phone ), [
				'name' => 'required|string|max:255',
				'role' => 'required|in:subscriber,school,group',
				'email' => 'required|email|max:255|unique:users',
				'password' => 'required|string|min:6',
				'phone' => 'required|string|min:10|max:10'
			]);
			
			//$validator = Validator::make($request->all(),$rules);
			
			
			if($validator->fails())
			{
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  400,
					'message'   =>  array( 'msg'=>$validator->messages()->first() )
				), 400);

				//return $validator->messages()->all();
			}

		   
		   
			$user = User::create([
				'name' => $request->name,
				'email' => $email,
				'role' =>  $request->role,
				'phone' => $request->phone,
				'password' => Hash::make($password),
			]);

			

			if($user){
				
				$usermeta = new Usermeta();

				$usermeta->user_id = $user->id;
				if($request->phone) $usermeta->mobile = $request->phone;
				if($request->gender) $usermeta->gender = $request->gender;
				if($request->dob) $usermeta->dob = $request->dob;
				if($request->age) $usermeta->age = $request->age;
				if($request->address) $usermeta->address = $request->address;
				if($request->pincode) $usermeta->pincode = $request->pincode;
				if($request->height) $usermeta->height = $request->height;
				if($request->weight) $usermeta->weight = $request->weight;
				if($request->state) $usermeta->state = $request->state;
				if($request->district) $usermeta->district = $request->district;
				if($request->block) $usermeta->block = $request->block;
				if($request->city) $usermeta->city = $request->city;
				if($request->udise) $usermeta->udise = $request->udise;
				if($request->orgname) $usermeta->orgname = $request->orgname;
				$usermeta->save();
			
			
			}
			
			
			
			if($user->id){
				
				if ( $token = auth('api')->attempt($validator->validated())) {
					//return $this->createNewToken($token);
					$usertoken = $this->createNewToken($token);
				//}
					
				return Response::json(array(
					'token' => $usertoken,
					'status'    => 'success',
					'code'      =>  200,
					'reqtime' => $request->reqtime,
					'message'   =>  array('msg'=>'User has been created successfully')
				), 200);
			}
			}
			//return $user;
			
		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
			

		}
    }


	
function update(Request $request){
        $user = auth('api')->user();
        if($user){
            $json_data = json_decode($request->json_val,true);
            if($json_data==null){
                $json_data = json_decode(base64_decode($request->json_val),true);
            }
            $user = User::find($user->id);
            $user->name = $json_data['name'];
            $user->save();

            $usermeta = Usermeta::where('user_id', $user->id)->first();
            if(!empty($json_data['phone'])) $usermeta->mobile = $json_data['phone'];
            if(!empty($json_data['gender'])) $usermeta->gender = $json_data['gender'];
            if(!empty($json_data['dob'])) $usermeta->dob = $json_data['dob'];
            if(!empty($json_data['age'])) $usermeta->age = $json_data['age'];
            if(!empty($json_data['address'])) $usermeta->address = $json_data['address'];
            if(!empty($json_data['pincode'])) $usermeta->pincode = $json_data['pincode'];
            if(!empty($json_data['height'])) $usermeta->height = $json_data['height'];
            if(!empty($json_data['weight'])) $usermeta->weight = $json_data['weight'];
            if(!empty($json_data['state'])) $usermeta->state = $json_data['state'];
            if(!empty($json_data['district'])) $usermeta->district = $json_data['district'];
            if(!empty($json_data['block'])) $usermeta->block = $json_data['block'];
            if(!empty($json_data['city'])) $usermeta->city = $json_data['city'];
            if(!empty($json_data['udise'])) $usermeta->udise = $json_data['udise'];
            if(!empty($json_data['orgname'])) $usermeta->orgname = $json_data['orgname'];

            $year = date("Y/m"); 
            if($request->file('profile_pic'))
            {
                $imageName1 = $request->file('profile_pic')->store($year,['disk'=> 'uploads']);
                $imageName1 = url('wp-content/uploads/'.$imageName1);
                $usermeta->image = $imageName1;
            }

            $usermeta->save();
            if($user->id){
				
				$data = User::join('usermetas', 'users.id', '=', 'usermetas.user_id')->where("users.id", $user->id)->get(['users.id','users.role','users.name', 'users.email', 'users.phone', 'usermetas.*']);
				return Response::json(array(
					'status'    => 'success',
					'code'      =>  200,
					'user'   =>  $data
					 ), 200);
				 
                return Response::json(array(
                    'status'    => 'success',
                    'code'      =>  200,
                    'message'   =>  array('msg'=>'User has been updated successfully')
                ), 200);
            }
        }else{
             return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
        }
    }
     function update_new(Request $request){
        $user = auth('api')->user();
        if($user){


            $json_data = json_decode($request->json_val,true);
            $user = User::find($json_data['id']);
            $user->name = $json_data['name'];
            $user->save();

            $usermeta = Usermeta::where('user_id', $json_data['id'])->first();
            if(!empty($json_data['phone'])) $usermeta->mobile = $json_data['phone'];
            if(!empty($json_data['gender'])) $usermeta->gender = $json_data['gender'];
            if(!empty($json_data['dob'])) $usermeta->dob = $json_data['dob'];
            if(!empty($json_data['age'])) $usermeta->age = $json_data['age'];
            if(!empty($json_data['address'])) $usermeta->address = $json_data['address'];
            if(!empty($json_data['pincode'])) $usermeta->pincode = $json_data['pincode'];
            if(!empty($json_data['height'])) $usermeta->height = $json_data['height'];
            if(!empty($json_data['weight'])) $usermeta->weight = $json_data['weight'];
            if(!empty($json_data['state'])) $usermeta->state = $json_data['state'];
            if(!empty($json_data['district'])) $usermeta->district = $json_data['district'];
            if(!empty($json_data['block'])) $usermeta->block = $json_data['block'];
            if(!empty($json_data['city'])) $usermeta->city = $json_data['city'];
            if(!empty($json_data['udise'])) $usermeta->udise = $json_data['udise'];
            if(!empty($json_data['orgname'])) $usermeta->orgname = $json_data['orgname'];

            $year = date("Y/m"); 
            if($request->file('profile_pic'))
            {
                $imageName1 = $request->file('profile_pic')->store($year,['disk'=> 'uploads']);
                $imageName1 = url('wp-content/uploads/'.$imageName1);
                $usermeta->image = $imageName1;
            }

            $usermeta->save();
            if($user->id){
				
				$data = User::join('usermetas', 'users.id', '=', 'usermetas.user_id')->where("users.id", $user->id)->get(['users.id','users.role','users.name', 'users.email', 'users.phone', 'usermetas.*']);
				return Response::json(array(
					'status'    => 'success',
					'code'      =>  200,
					'user'   =>  $data
					 ), 
					 200
				 );
				 
               
            }
        }else{
             return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
        }
    }
    
	
	
	
	public function logout() {
       $logout = auth('api')->logout();

       return Response::json(array(
        'status'    => 'success',
        'code'      =>  200,
        'message'   =>  'User successfully signed out'
        ), 200);

       

       // return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request) {

      // return response()->json(auth('api')->user());
       $user = auth('api')->user();
       

        if($user){

            $data = User::join('usermetas', 'users.id', '=', 'usermetas.user_id')->where("users.id", $user->id)
       ->get(['users.id','users.role','users.name', 'users.email', 'users.phone', 'usermetas.*']);
            return Response::json(array(
                'status'    => 'success',
                'code'      =>  200,
                'user'   =>  $data
                 ), 200);

        }else{
            return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
        }    
    }
	

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $user = auth('api')->user();
        
        $data = User::join('usermetas', 'users.id', '=', 'usermetas.user_id')->where("users.id", $user->id)
        ->get(['users.id', 'users.role', 'users.name', 'users.email', 'users.phone', 'usermetas.user_id', 'usermetas.dob', 'usermetas.age', 'usermetas.gender', 'usermetas.address', 'usermetas.state', 'usermetas.district', 'usermetas.block', 'usermetas.city', 'usermetas.orgname', 'usermetas.udise', 'usermetas.pincode', 'usermetas.height', 'usermetas.weight', 'usermetas.image', 'usermetas.board',
'usermetas.created_at', 'usermetas.updated_at' ]);
        


        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            //'user' => auth('api')->user()->usermeta()
            'user' => $data
        ]);
    }

	
}
