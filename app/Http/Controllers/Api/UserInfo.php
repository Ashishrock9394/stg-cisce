<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use App\Models\UserInfos;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class UserInfo extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
	
	public function index(){
		$user = auth('api')->user();
		if($user->id){
		$results = UserInfos::where('user_id',$user->id)->first();
		if(!empty($results)){
			
		
		return Response::json( 
		array(
            'status'    => 'success',
            'code'      =>  200,
            'user'   => json_decode($results->info, true)
            ), 200);
		}else{
			return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
		}
		
		}else{
            return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
        }	
	}
	
	public function store(Request $request){
		
		$user = auth('api')->user();
		
		if($user->id){
		
		$results = UserInfos::where('user_id',$user->id)->first();
		
			if(!empty($results->user_id)){
				
				$results = DB::table('userinfo')->where('user_id', $user->id)->update([
					'info' => $request->info
				]);
				
				
				/*
				$results = UserInfos::where('user_id',$user->id);
				$user = $results->update([
					'info' => $request->info
				]);
				*/
				if(!empty($results)){
					return Response::json( 
						array(
							'status'    => 'success',
							'code'      =>  200,
							'user'   =>  "User info updated successfully"
							), 
						200);
				}else{
					return Response::json(array(
					'status'    => 'error',
					'code'      =>  401,
					'message'   =>  'not updated'
				), 401);
				}
			}else{
				$user = UserInfos::create([
					'user_id' => $user->id,
					'info' => $request->info
				]);
				if(!empty($user)){
					return Response::json( 
						array(
							'status'    => 'success',
							'code'      =>  200,
							'user'   =>  "User info added successfully"
							), 
						200);
				}else{
					return Response::json(array(
					'status'    => 'error',
					'code'      =>  401,
					'message'   =>  'not inserted'
				), 401);
				}
			}
		
		}else{
            return Response::json(array(
                'status'    => 'error',
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ), 401);
        }	
		
		
		
	}
}
