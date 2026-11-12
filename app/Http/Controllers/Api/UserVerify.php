<?php
namespace App\Http\Controllers\Api;
use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use App\Models\Userverification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\OtpTrack;

class UserVerify extends Controller
{	
	private $OPENSSL_CIPHER_NAME = "aes-128-cbc"; //Name of OpenSSL Cipher 
    private $CIPHER_KEY_LEN = 16; 
	
	function decrypt($key, $iv, $data) {
        if(strlen($key) < $this->CIPHER_KEY_LEN) {
            $key = str_pad("$key", $this->CIPHER_KEY_LEN, "0"); 
        } else if (strlen($key) > $this->CIPHER_KEY_LEN) {
            $key = substr($str, 0, $this->CIPHER_KEY_LEN); 
        }
       
        $decryptedData = openssl_decrypt( base64_decode($data), $this->OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedData;
    }	
     
    public function verify_user_email(Request $request){
       	try{ 
			$iv = "fedcba9876543210"; 
			$key = "0a9b8c7d6e5f4g3h";
				
				if(strpos($request->reqtime, '=') == false){
					return Response::json(array(
						'status'    => 'error',
						'code'      =>  422,
						'message'   =>  'Not valid request'
					), 422);
				}				
				
								
				 
			$reqtimevar = $this->decrypt($key, $iv, $request->reqtime);
			
							
			$key = $reqtimevar . 'fitind';
			$email = $this->decrypt($key, $iv, $request->email);//phone number sms send	

            if(is_numeric($email)){				
				$validator = Validator::make(
					array( "phone" => $email),['phone' => 'required|digits:10']
				);
				
			}else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
				$validator = Validator::make( array( "email" => $email ), ['email' => 'required|email']);
				
			}else{
				return Response::json(array(
					'status' => 'error', 'code'  =>  422, 'message'   =>  'Invalid Input'
				), 422);
			}	           
							
								
				if($validator->fails()){
					return Response::json(array(
						'status'    => 'error',
						'code'      =>  422,
						'message'   =>  $validator->messages()->first()
					), 422);
				}

				$otp = mt_rand(100000,999999);	
				$phone_otp = '123456';
				$cflag='';
				
                $start = date( "Y-m-d 00:00:00");
				$end = date( "Y-m-d 23:59:59");				
				
				if(is_numeric($email)){						
						
					$userverification = Userverification::where('phone', $email)->first();
					$otpcnt = OtpTrack::where('phone', $email)->where('type','user')->whereBetween('created_at',[$start,$end])->count();
					$cflag='0';
				} else if(filter_var($email, FILTER_VALIDATE_EMAIL)){					
									
					$userverification = Userverification::where('email', $email)->first();
                    $otpcnt = OtpTrack::where('email', $email)->where('type','user')->whereBetween('created_at',[$start,$end])->count();
                    $cflag='1';					
				}							
				
								
				if($otpcnt > 2){				
					return Response::json(array(
							'status'    => 'error',
							'code'      =>  401,
							'message'   =>  'Your request limit exceeds'
						), 401);
				}
					
				  
					if(!empty($userverification)){
						
						if(empty($userverification->isverified)){
							
							if($cflag==1){								
														
								Userverification::where('email', $email)->update(['otp' => $otp]);
								$this->send($email,$otp);							
								$otptrc = new OtpTrack();
								$otptrc->email = $email;
								$otptrc->otp = $otp;
								$otptrc->type = 'user';
								$otptrc->save();
					
								return response()->json([
								'success' => true,
								'message' => 'OTP successfully has been sent', 
								'reqtime' => $request->reqtime,
								], 200);	
								
							} else if($cflag==0){
								
								Userverification::where('phone', $email)->update(['otp' => $phone_otp]);	 
							    							
								$otptrc = new OtpTrack();
								$otptrc->phone = $email;
								$otptrc->otp = $phone_otp;
								$otptrc->type = 'user';
								$otptrc->save();
					
								return response()->json([
								'success' => true,
								'message' => 'Phone OTP successfully updated', 
								'reqtime' => $request->reqtime,
								], 200);								
							}
							
							
						} else {
							
							return response()->json([
							'success' => true,
							'message' => 'You are already Verified', 
							'reqtime' => $request->reqtime,
							], 200);
						}

					} else {

						 if($cflag==1){							 
							
                            $userv = new Userverification();
							$userv->email = $email;
							$userv->otp = $otp;						
							$userv->save();							
							$this->send($email,$otp);
							
							$otptrc = new OtpTrack();
							$otptrc->email = $email;
							$otptrc->otp = $otp;
							$otptrc->type = 'user';
							$otptrc->save();
								
							return response()->json([
								'success' => true,
								'message' => 'OTP successfully has been sent', 
								'reqtime' => $request->reqtime,
								], 200);							
								 
						 } else if($cflag==0){						 
							
							$userv = new Userverification();
							$userv->phone = $email;
							$userv->otp = $phone_otp;						
							$userv->save();
							
													
							$otptrc = new OtpTrack();
							$otptrc->phone = $email;
							$otptrc->otp = $phone_otp;
							$otptrc->type = 'user';
							$otptrc->save();
								
							return response()->json([
								'success' => true,
								'message' => 'Phone OTP successfully updated', 
								'reqtime' => $request->reqtime,
								], 200);							 
						 }						

										
					}					

		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
		}			
	} 

	public function send($email,$msg){
      $otp = $msg;
      $msg = '<!DOCTYPE HTML><html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<title>FIT INDIA Email verification OTP</title>
				<style>.yada{color:green;}</style>
			</head>
			<body>
				<p>Dear FitIndia user,</p>
				<br>
				<p>Welcome, We thank you for your registration at FitIndia mobile app.</p>
				<p>Your user id is <'.$email.'> </p>
				<p>Your email id Verification OTP code is : '.$otp.'</p>
				<p>You will use this user id given above for all activities on FitIndia mobile app. The user id cannot be changed and hence we recommend that you store this email for your future reference.</p>
				<p>Regards, <br> Fit India Mission</p>				
			</body>
			</html>';						
				
		  $curl = curl_init();
		  curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => 						  
		  json_encode(
			array(
				"sender" => array( "name" => "Fit India App","email" => "no-reply@fitindia.gov.in" ),
				"to" => [["email" => $email]],
				"htmlContent" => $msg,
				"subject" => "Fit India user email verification OTP"
			  )																
			),
			
			CURLOPT_HTTPHEADER => [
				"Accept: application/json",
				"Content-Type: application/json",
				  ],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			if($err){
			  echo "cURL Error #:" . $err;
			} else {
				$response = json_decode($response);
			    // print_r($response);
			    if(!empty($response->messageId)){   }
			}
    } 	

    public function verifyuser(Request $request){		
	   //dd($request);		
	   try{ 
			$iv = "fedcba9876543210"; 
			$key = "0a9b8c7d6e5f4g3h";
				
				if(strpos($request->reqtime, '=') == false) {
					return Response::json(array(
						'status'    => 'error',
						'code'      =>  422,
						'message'   =>  'Not valid request'
					), 422);
				}
				
				if(strpos($request->otp, '=') == false) {
					return Response::json(array(
						'status'    => 'error',
						'code'      =>  422,
						'message'   =>  'Not valid otp'
					), 422);
				}
				
				
								
				 
			$reqtimevar = $this->decrypt($key, $iv, $request->reqtime);
			//$rcaptchavar = $this->decrypt($key, $iv, $request->rcaptcha);
			$otp = $this->decrypt($key, $iv, $request->otp);
			
			
				
			$key = $reqtimevar . 'fitind';
			$email = $this->decrypt($key, $iv, $request->email);

           			
            if($otp){             
			  $validator = Validator::make(
			   array("otp" => $otp),[				
				'otp' => 'required|regex:/\b\d{6}\b/']
			  );			

            } else if(is_numeric($email)){				
				$validator = Validator::make(
					array( "phone" => $email),['phone' => 'required|digits:10']
				);
				
			} else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
				$validator = Validator::make( array( "email" => $email ), ['email' => 'required|email']);
				
			} else{
				return Response::json(array(
					'status' => 'error', 'code'  =>  422, 'message'   =>  'Invalid Input'
				), 422);
			}			
		
		   
			if($validator->fails()) {
				$error = $validator->errors()->first();
				return response()->json([
				'success' => false,
				'status' => 'error',
				'code' => 400,
				'message' => $error,         
				], 400);
			}
			
			$cflag='';
			
			if(is_numeric($email)){						
						
				$verifyUser = Userverification::where('phone', $email)->first();				
				$cflag='0';
			} else if(filter_var($email, FILTER_VALIDATE_EMAIL)){					
				
				$verifyUser = Userverification::where('email', $email)->first();				
				$cflag='1';					
			}		
		 
					   
		if(!empty($verifyUser)){			
			
			if(empty($verifyUser->isverified)){

             if($cflag==1){								
	            //echo $otp."aaaa".$email; die;
				if($otp == $verifyUser->otp){				
					
									
					$userverf = User::where('email', $email)->first();
					
					if(!empty($userverf)){	
					
						User::where('email', $email)->update(['verified' => '1']);
					}	
						Userverification::where('email', $email)->update(['isverified' => '1']);
						
						return response()->json([
						'success' => true,
						'status'    => 'sucess',
						'code'      =>  200,
						'reqtime' => $request->reqtime,
						'message' => 'Your e-mail is verified',         
						], 200);						
				  
				}else{
					return Response::json(array(
					'status'    => 'error',
					'success' => false,
					'code'      =>  422,
					'message'   =>  'OTP does not match'
					), 422);
				}
				
			 } else if($cflag==0){
				 
				 if($otp == $verifyUser->otp){				
					
										
					$userverf = User::where('phone', $email)->first();
					
					if(!empty($userverf)){	
					
						User::where('phone', $email)->update(['verified' => '1']);
					}	
						Userverification::where('phone', $email)->update(['isverified' => '1']);
						
						return response()->json([
						'success' => true,
						'status'    => 'sucess',
						'code'      =>  200,
						'reqtime' => $request->reqtime,
						'message' => 'Your phone number is verified',         
						], 200);						
				  
				} else {
					return Response::json(array(
					'status'    => 'error',
					'success' => false,
					'code'      =>  422,
					'message'   =>  'OTP does not match'
					), 422);
				}
				 
				
			 }		
			  
			  
			} else {
			  
			  return response()->json([
				'success' => true,
				'status'    => 'sucess',
				'code'      =>  200,
				'reqtime' => $request->reqtime,
				'message' => 'Your data'.$email.' is already verified',         
				], 200);
			}

		  } else {

			 return Response::json(array(
					'status'    => 'error',
					'success' => false,
					'code'      =>  422,
					'message'   =>  'Sorry your '.$email.' cannot be identified'
				), 422);
		   }

		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
		  }		
	  }	   
	  
 }