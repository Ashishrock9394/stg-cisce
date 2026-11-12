<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Water; 
use Illuminate\Support\Facades\DB;

class WaterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response

     */
	 
	public function goal(Request $request)
    {
		//$user = auth('api')->user();
        if($request->user_id){
			
			$fordate = str_replace('/','-',$request->createdOn);
			$datevar = date( "Y-m-d", strtotime($fordate) );
			
			$water = Water::where('user_id',$request->user_id)->where('for_date',$datevar)->first();
			
            if (!is_null($water) && !is_null($request->water_goal) ){
				$updststaus  = $water->update([
					'goal' => $request->water_goal,
				]);
				
				if($updststaus){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Water goal sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 304,
                        'message' => 'Water goal not updated'
                    ), 304); 
                }
				
			}else{
				
				
				$fordate = str_replace('/','-',$request->createdOn);
				$datevar = date( "Y-m-d", strtotime($fordate) );
				$water = new Water();
                $water->user_id = $request->user_id;    
                $water->goal = $request->water_goal;
                $water->for_date = $datevar;
               
                if($water->save()){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Water goal sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 304,
                        'message' => 'Water goal not updated'
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
		//dd($request->all());
	$arruserId = explode(",",$request->user_id);
	//dd($arrUsers);
		//$user = auth('api')->user();
		if($request->user_id){
			if(!empty($request->created_on)){
					$datevar = date( "Y-m-d", strtotime($request->created_on) );
					$water = DB::table('waters')
							->select( DB::raw("id, user_id, quantity, goal, weekly_completion, DATE_FORMAT( for_date, '%Y/%m/%d' ) as for_date" )  )
							->whereIn('user_id',$arruserId)->where( 'for_date', $datevar )
							->get();
				
				
				return Response::json(array(
						'status'    => 'success',
						'code'      =>  200,
						'message'   =>  $water
					), 200);	
					
			}else{
				$water = DB::table('waters')
							->select( DB::raw("id, user_id, quantity, goal, weekly_completion, DATE_FORMAT( for_date,'%Y/%m/%d' ) as for_date ") )
							->whereIn('user_id',$arruserId)
							->get();
							 
				return Response::json(array(
						'status'    => 'success',
						'code'      =>  200,
						'message'   =>  $water
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
        //$user = auth('api')->user();
        if($request->user_id){
			$fordate = str_replace('/','-',$request->createdOn);
			$datevar = date( "Y-m-d", strtotime($fordate) );
			
			$water = Water::where('user_id',$request->user_id)->where('for_date',$datevar)->first();
			
            if (!is_null($water)){
				$updststaus  = $water->update([
					'quantity' => $request->water_qty,
					'goal' => $request->water_goal,
					'weekly_completion' => $request->weekly_completion,
					
				]);
				
				if($updststaus){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Water sucessfully updated'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Water not stored'
                    ), 200); 
                }
				
			}else{
				$fordate = str_replace('/','-',$request->createdOn);
				
				$datevar = date( "Y-m-d", strtotime($fordate) );
				$water = new Water();
                $water->user_id = $request->user_id;    
                $water->quantity = $request->water_qty;
                $water->goal = $request->water_goal;
                $water->weekly_completion = $request->weekly_completion;
                $water->for_date = $datevar;
               

                if($water->save()){
                    return Response::json(array(
                        'statue' => 'success',
                        'code' => 200,
                        'message' => 'Water sucessfully stored'
                    ), 200);
                }else{
                    return Response::json(array(
                        'statue' => 'error',
                        'code' => 200,
                        'message' => 'Water not stored'
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
