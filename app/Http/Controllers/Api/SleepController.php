<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Sleep;
use App\Models\User;
use App\Models\UserGoal;
use Illuminate\Support\Facades\DB;


class SleepController extends Controller
{
    
	public function goal(Request $request)
    {
		
		
		//$user = auth('api')->user();
        if($request->user_id){
			
			$fordate = str_replace('/','-',$request->createdOn);
			$datevar = date( "Y-m-d", strtotime($fordate) );
			
			$sleep = Sleep::where('user_id', $request->user_id)->where('wakup_date',$datevar)->first();
			
			//return $sleep;
            if (!is_null($sleep) && !is_null($request->goal) ){
				$status  = $sleep->update([
					'goal' => $request->goal,
				]);
				
				if($status){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Sleep goal sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Sleep goal not updated'
                    ), 200); 
                }
				
			}else{
				$sleep = new Sleep();
				$sleep->user_id = $request->user_id;
				$sleep->goal = $request->goal;
				$sleep->wakup_date = $datevar;
				$status = $sleep->save(); 
				
				if($status){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Sleep goal sucessfully added'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Sleep goal not added'
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
	
    public function index(Request $request)
    {
		//$user = auth('api')->user();
		$arruserId = explode(",",$request->user_id);
        if($request->user_id){
			

			if(!empty($request->created_on)){
				$fordate = str_replace('/','-',$request->created_on);
				
				$datevar = date( "Y-m-d", strtotime($fordate) );
				$startdate = $datevar.' 00:00:00';
				$enddate = $datevar.' 23:59:59';
				
				//$sleep = Sleep::where( 'user_id', $request->user_id )->whereBetween( 'created_at', [$startdate, $enddate] )->get();
				$sleep = DB::table('sleep')->select(DB::raw("sleep.id, sleep.user_id, bed_date, bed_time, wakup_date, wakup_time, sleep_hours, goal_achieve ,goal, DATE_FORMAT(sleep.created_at,'%Y/%m/%d') as created_at" ))
						 ->whereIn( 'sleep.user_id', $arruserId )->whereBetween( 'sleep.created_at', [$startdate, $enddate] )
						 ->get();
			}else{
				//$sleep = Sleep::select('id', 'user_id', 'bed_date', 'bed_time', 'wakup_date', 'wakup_time', 'sleep_hours', 'goal_achieve', 'created_at')->where('user_id',$request->user_id)->get();
				$sleep = DB::table('sleep')->select(DB::raw("sleep.id, sleep.user_id, bed_date, bed_time, wakup_date, wakup_time, sleep_hours, goal_achieve ,goal, DATE_FORMAT(sleep.created_at,'%Y/%m/%d') as created_at" ))
						 ->whereIn('sleep.user_id',$arruserId)
						 //->where('user_goals.type','=','sleep')
						 ->get(); 
			}
			
			return Response::json(array(
					'status'    => 'success',
					'code'      =>  200,
					'message'   =>  $sleep
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
			$wakupdate = str_replace('/','-',$request->wakup_date);
			$beddate = str_replace('/','-',$request->bed_date);
			
			$wakeupdate = date("Y-m-d", strtotime($wakupdate));
			$beddate = date("Y-m-d", strtotime($beddate));
			
            $sleep = Sleep::where('user_id',$request->user_id)->where('wakup_date',$wakeupdate)->first();
			
            if (!is_null($sleep)){
			/*
                $sleep->user_id = $request->user_id;    
                $sleep->bed_date = $beddate;
                $sleep->bed_time = $request->bed_time;
                $sleep->wakup_date = $wakeupdate;
                $sleep->wakup_time = $request->wakup_time;
				*/
                $date1 = "$beddate $request->bed_time";
                $date2 = "$wakeupdate $request->wakup_time";
				
				$seconds = strtotime($date2) - strtotime($date1);
                $hours = round($seconds / 60 /  60);
               // $sleep->sleep_hours = $request->sleep_hours;
               // $sleep->goal_achieve = $request->goal_achieve;
			   $sleeparr = array(
					'bed_date' => $beddate,
					'bed_time' => $request->bed_time,
					'wakup_date' => $wakeupdate,
					'wakup_time' => $request->wakup_time,
					'sleep_hours' => $request->sleep_hours
					);
					
				
				if(!empty($request->goal)){
					$sleeparr['goal'] = $request->goal;
				}
				
				$sleep->update($sleeparr);
			

                if($sleep){
                    return Response::json(array(
                        'hours_count' => $request->sleep_hours,
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Time sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Time not stored'
                    ), 200); 
                }
            }else{
				
                $sleep_user = new Sleep();
                $sleep_user->user_id = $request->user_id;    
                $sleep_user->bed_date = $beddate;
                $sleep_user->bed_time = $request->bed_time;
                $sleep_user->wakup_date = $wakeupdate;
                $sleep_user->wakup_time = $request->wakup_time;
                $date1 = "$beddate $request->bed_time";
                $date2 = "$wakeupdate $request->wakup_time";
                $seconds = strtotime($date2) - strtotime($date1);
                $hours = $seconds / 60 /  60;
                $sleep_user->sleep_hours = $request->sleep_hours;
				if(!empty($request->goal)){
					$sleep_user->goal = $request->goal;
				}
                //$sleep_user->goal_achieve = $request->goal_achieve;

                if($sleep_user->save()){
                    return Response::json(array(
                        'hours_count' => $request->sleep_hours,
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Time sucessfully stored'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Time sucessfully stored'
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
     * @param  \App\Models\Sleep  $sleep
     * @return \Illuminate\Http\Response
     */
    public function show(Sleep $sleep)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sleep  $sleep
     * @return \Illuminate\Http\Response
     */
    public function edit(Sleep $sleep)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sleep  $sleep
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sleep $sleep)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sleep  $sleep
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sleep $sleep)
    {
        //
    }
}
