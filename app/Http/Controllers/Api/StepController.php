<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Step;
use App\Models\User;
use Illuminate\Support\Facades\DB;;

class StepController extends Controller
{
   
  public function goal(Request $request)
    {
		//$user = auth('api')->user();
        if($request->user_id){
			
			$fordate = str_replace('/','-',$request->createdOn);
			$datevar = date( "Y-m-d", strtotime($fordate) );
			$step = Step::where('user_id',$request->user_id)->where('for_date',$datevar)->first();
			
            if (!is_null($step) && !is_null($request->goal) ){
				$updststaus  = $step->update([
					'goal' => $request->goal,
					
				]);
				
				if($updststaus){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Step goal sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Step goal not updated'
                    ), 200); 
                }
				
			}else{
                    
					$fordate = str_replace('/','-',$request->createdOn);
					$datevar = date( "Y-m-d", strtotime($fordate) );
					$step = new Step();
					$step->user_id = $request->user_id;    
					$step->goal = $request->goal;
					$step->for_date = $datevar;
				   
					if($step->save()){
						return Response::json(array(
							'statue' => 'success',
							'code' => 200,
							'message' => 'Step goal sucessfully updated'
						), 200);
					}else{
						return Response::json(array(
							'statue' => 'error',
							'code' => 304,
							'message' => 'Step goal not updated'
						), 304); 
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
    public function index(Request $request)
    {	

		//$user = auth('api')->user();
        if($request->user_id){
			
			$arrUsers = explode(",",$request->user_id);
			
			if(count($arrUsers) > 1)
				$allUsersData = 1;
			else
				$allUsersData = 0;
			
			 //DB::table('tempLog')->insert(array('logtext'=>$request->user_id."&".$allUsersData."-".count($arrUsers))); 
			/*
				SELECT max(id) as id, user_id, sum(steps) as steps, max(noofevent) as noofevent, sum(calorie) as calorie, max(point) as point, AVG(speed) as speed, sum(distance) as distance, avg(goal) as goal, 
				DATE_FORMAT( max(for_date), '%Y/%m/%d' ) as for_date FROM `steps` where user_id > 6111
			*/


			if($allUsersData == 0){
				
			if(!empty($request->created_on)){
					$datevar = date( "Y-m-d", strtotime($request->created_on) );
					$step = DB::table('steps')
							->select( DB::raw("id, user_id, steps, noofevent, calorie, point, speed, distance, goal, DATE_FORMAT( for_date, '%Y/%m/%d' ) as for_date" )  )
							->where('user_id',$request->user_id)->where( 'for_date', $datevar )
							->get();
				
				return Response::json(array(
						'status'    => 'success',
						'code'      =>  200,
						'message'   =>  $step
					), 200);	
					
			}else{
				$step = DB::table('steps')
							->select( DB::raw("id, user_id, steps, noofevent, calorie, point, speed, distance, goal, DATE_FORMAT( for_date,'%Y/%m/%d' ) as for_date ") )
							->where('user_id',$request->user_id)
							->get();
							 
				return Response::json(array(
						'status'    => 'success',
						'code'      =>  200,
						'message'   =>  $step
					), 200);
			}
			}
			else
			{
				
				//DB::enableQueryLog(); // Enable query log

				if(empty($request->created_on)){
							
					$step = DB::select("SELECT max(id) as id, max(user_id) as user_id, sum(steps) as steps, max(noofevent) as noofevent, sum(calorie) as calorie, max(point) as point, AVG(speed) as speed, sum(distance) as distance, sum(goal) as goal, 
				DATE_FORMAT( max(for_date), '%Y/%m/%d' ) as for_date FROM `steps` where user_id >=".$arrUsers[0]." and user_id < ".$arrUsers[1]."  
				group by user_id ");
				}else {
					$datevar = date( "Y-m-d", strtotime($request->created_on) );
					$step = DB::select("SELECT max(id) as id, max(user_id) as user_id, sum(steps) as steps, max(noofevent) as noofevent, sum(calorie) as calorie, max(point) as point, AVG(speed) as speed, sum(distance) as distance, sum(goal) as goal, 
				DATE_FORMAT( max(for_date), '%Y/%m/%d' ) as for_date FROM `steps` where user_id >=".$arrUsers[0]." and user_id < ".$arrUsers[1]." and STR_TO_DATE(for_date,'%Y-%m-%d')>= STR_TO_DATE('".$datevar."','%Y-%m-%d')    
				group by user_id ");
				}
				
				

				//dd(DB::getQueryLog()); // Show results of log
				
				return Response::json(array(
						'status'    => 'success',
						'code'      =>  200,
						'message'   =>  $step
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // $user = auth('api')->user();
        if($request->user_id){
			
			$fordate = str_replace('/','-',$request->createdOn);
			$datevar = date( "Y-m-d", strtotime($fordate) );
			$step = Step::where('user_id',$request->user_id)->where('for_date',$datevar)->first();
			
            if (!is_null($step)){
				$steparr = array();
				if(!empty($request->steps)){ 	$steparr['steps'] = $request->steps; 	}
				if(!empty($request->calorie)){ $steparr['calorie'] = $request->calorie;	}
				if(!empty($request->point)){ $steparr['point'] = $request->point;	}
				if(!empty($request->speed)){ $steparr['speed'] = $request->speed;	}
				if(!empty($request->distance)){	$steparr['distance'] = $request->distance;	}
				if(!empty($request->noofevent)){ $steparr['noofevent'] = $request->noofevent;	}
				if(!empty($request->goal)){	$steparr['goal'] = $request->goal;	}
				
				$updststaus  = $step->update( $steparr );
				
				if($updststaus){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Step sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Step not stored'
                    ), 200); 
                }
				
			}else{
				$fordate = str_replace('/','-',$request->createdOn);
				$datevar = date( "Y-m-d", strtotime($fordate) );
				$step = new Step();
                $step->user_id = $request->user_id;    
                $step->steps = $request->steps;
                $step->noofevent = $request->noofevent;
                $step->calorie = $request->calorie;
				$step->point = $request->point;
				$step->speed = $request->speed;
				$step->distance = $request->distance;
                $step->for_date = $datevar;
				$step->goal = $request->goal;
				

                if($step->save()){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Step sucessfully stored'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Step not stored'
                    ), 200); 
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
