<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendMailreset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RequestHelper;//updatePassword

class ChanagePasswordController extends Controller { 

   //public function updatePassword(RequestHelper $request){   
	
	private $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher 
    private $CIPHER_KEY_LEN = 16; 
	
	function decrypt($key, $iv, $data) {
        if (strlen($key) < $this->CIPHER_KEY_LEN) {
            $key = str_pad("$key", $this->CIPHER_KEY_LEN, "0"); 
        } else if (strlen($key) > $this->CIPHER_KEY_LEN) {
            $key = substr($str, 0, $this->CIPHER_KEY_LEN); 
        }
       
        $decryptedData = openssl_decrypt( base64_decode($data), $this->OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedData;
    }
	
	
	public function updatePassword(Request $request){  

       // return $this->changePassword($request);
	   
	   
	   try{ 
			$iv = "fedcba9876543210"; 
			$key = "0a9b8c7d6e5f4g3h"; 
			
			$data = $request->email;
			

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
			
			$email = $this->decrypt($key, $iv, $request->email);
			$password = $this->decrypt($key, $iv, $request->password);
			
		
			

			$validator = Validator::make(array("email"=>$email, "password"=>$password), [
				'email' => 'required|email',
				'password' => 'required|string|min:6',
			]);
			
			if ($validator->fails()) {
				return Response::json(array(
					'status'    => 'error',
					'code'      =>  422,
					'message'   =>  $validator->messages()->first()
				), 422);

			}
			
				$user = User::whereEmail($email)->first();
				if(!empty($user)){
					$res = $user->update([
					  'password'=>Hash::make($password)
					]);
					
					if($res){
						return response()->json([
								'success' => true,
								'status' => 'success', 
								'code' => 200,
								'reqtime' => $request->reqtime,
								'message' => 'User Password changed successfully'       
							], 200);
							
					}else{
						
						return response()->json([
								'success' => false,
								'status' => 'error', 
								'code' => 400,
								'message' => 'User password not changed'       
							], 400);
							
					}
				}else{
					return response()->json([
								'success' => false,
								'status' => 'error', 
								'code' => 404,
								'message' => 'User not found'       
							], 400);
				}
				
		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
			

		}
    }

    private function validateToken($request){
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token            
        ]);
    }

    private function noToken(){
        return response()->json([
          'error' => 'Email or token does not exist.'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword($request) {
		
        $user = User::whereEmail($request->email)->first();
        $res = $user->update([
          'password'=>Hash::make($request->password)
        ]);
		
		if($res){
			return response()->json([
					'success' => true,
					'status' => 'success', 
					'code' => 200,
					'message' => 'User Password changed successfully'       
				], 200);
				
		}else{
			
			return response()->json([
					'success' => false,
					'status' => 'error', 
					'code' => 400,
					'message' => 'User password not changed'       
				], 400);
				
		}
		
		
       /* $this->validateToken($request)->delete();
        return response()->json([
          'data' => 'Password changed successfully.'
        ],Response::HTTP_CREATED);
		*/
		
    }  
}