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

class PasswordResetRequestController extends Controller{    
	
	public function sendEmail(Request $request){
      
        if(!$this->validEmail($request->email)){
            return response()->json([
                'message' => 'Email not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $this->send($request->email);
            return response()->json([
                'message' => 'Password reset mail has been sent.'
            ], Response::HTTP_OK);            
        }
     }	
	
	  public function send($email){

        $token = $this->createToken($email);
     
  $site_url=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  $url_link=explode('api/', $site_url); 

     
            $curl = curl_init();

            $linkurl= $url_link[0].'update-password?token='.$token;

            $chagepsd='<center><b><a href="'.$linkurl.'">Please Click here for Change Your Password </a></b></center>';

            $senddata = array (
                'sender' => array('name'=>'nagendra.kumar','email'=>$email),
                'to' => array(array('email'=>'nagendra.kumar@seqfast.com')),               
                'textContent' =>$chagepsd ,                
                'subject' => 'Password reset link'                
            );               

          curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>json_encode($senddata),          
          CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }
	
	
	public function createToken($email){
		
      $isToken = DB::table('password_resets')->where('email', $email)->first();

      if($isToken) {
        return $isToken->token;
      }

      $token = Str::random(80);;
      $this->saveToken($token,$email);
      return $token;
    }
	
	
	public function saveToken($token, $email){
		
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()            
        ]);
    }
	
	public function validEmail($email) {
		
       return !!User::where('email', $email)->first();
    }	
	
}
