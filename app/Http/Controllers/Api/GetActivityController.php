<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request,Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Subject;
use App\Models\Activityrelation;
use App\Models\Chapter;
use App\Models\Concept;
use App\Models\Sclass;
use App\Models\Classactivity;
use App\Models\Subjectactivity;
use App\Models\Chapteractivity;
use App\Models\Conceptactivity;
use App\Models\Skillactivity;
use App\Models\Categoryactivity;
use App\Models\Sportactivity;
use App\Models\Class_subject;

class GetActivityController extends Controller{	
	
	public function getclass(Request $request){		
		
		$data = DB::table('class')->select(DB::raw('DISTINCT id,name'));
		
		if($request->class_id){
			
		   $data = $data->where('id', '=', $request->class_id );
		}		
	  
		 $class=$data->where('class.status','=','1')
		             ->where('name','!=','null')
				     ->orderBy('id','asc')
				     ->get();					
				
		  return Response::json(array(
			'status'    => 'success',
			'code'      =>  200,
			'message'   => (!empty($class)? $class : 'Data not found.'), 
		), 200);
	}
	
	public function getsubject(Request $request){	
				
       $data = DB::table('subject')
				->leftJoin('class_subject','class_subject.subject_id', '=','subject.id')				
				->select(DB::raw("subject.id,subject.name,subject.status,class_subject.subject_id,class_subject.class_id"));
			  
	   	  
		
		if($request->class_id){
		
		  $data = $data->where('class_subject.class_id','=',$request->class_id);
		}		
		
		if($request->subject_id){
			
			$data = $data->where('class_subject.subject_id','=',$request->subject_id);
		}
		
		if($request->id){
			
		   $data = $data->where('subject.id', '=', $request->id );			  
		}	
					  
		$subject=$data->where('subject.status','=','1')->get();

        //dd(DB::getQueryLog());		 
		
		  return Response::json(array(
			'status'    => 'success',
			'code'      =>  200,
			'message'   => (!empty($subject)? $subject : 'Data not found.'), 
		), 200);
	}
	
	public function getchapter(Request $request){		
		 
		$data = DB::table('chapter')->select(['chapter.*']);
		
		if($request->cid){
		   $data = $data->where('chapter.id', '=', $request->cid );			  
		}
		
		if($request->class_id){				
		   $data = $data->where('chapter.class_id', '=', $request->class_id );
		}

		if($request->subject_id){
		   $data = $data->where('chapter.subject_id', '=', $request->subject_id );			  
		}		
					
		$chapter=$data->where('chapter.status','=','1')->get();
		
		$kchapter=array();	
		
		 if(!empty($chapter)){
			  foreach($chapter as $cval){	
                //echo "<pre>";print_r($cval); 
				if($cval->image!=''){					
				  $chapimg = asset('/public/uploads/'.$cval->image);       
				}else{					
				  $chapimg = asset('/resources/images/default-chapter-img.png');					
				}					
				
				$chapdata=array(
						"id" => $cval->id,
						"class_id" => $cval->class_id,
						"subject_id" => $cval->subject_id,
						"name" => $cval->name,
						"description" => htmlentities($cval->description),
						"image" => $chapimg,							
						"url" => $cval->url,							
						"learning_outcomes" => htmlentities($cval->learning_outcomes),							
						"unit" => $cval->unit,							
						"book" => $cval->book,							
						"order" => $cval->order,							
						"status" => $cval->status
				   );
					
				  array_push($kchapter,$chapdata);	
			    }				
			}
	  				
		  return Response::json(array(
			'status'    => 'success',
			'code'      =>  200,
			'message'   => (!empty($kchapter)? $kchapter : 'Data not found.'),   
		  ), 200);
	}	
	
	public function getconcept(Request $request){		
		 
		$data = DB::table('concept')->select(['concept.*']);
		
		if($request->cid){
		   $data = $data->where('concept.id', '=', $request->cid );			  
		}
		
		if($request->class_id){				
		   $data = $data->where('concept.class_id', '=', $request->class_id );
		}

		if($request->subject_id){
		   $data = $data->where('concept.subject_id', '=', $request->subject_id );			  
		}
		
		if($request->chapter_id){
		   $data = $data->where('concept.chapter_id', '=', $request->chapter_id );			  
		}		
					
		$concept=$data->where('concept.status','=','1')->get();
		
		$kconcept=array();	
		
		 if(!empty($concept)){
			  foreach($concept as $cval){	               
				if($cval->image!=''){					
				  $consimg = asset('/public/uploads/'.$cval->image);       
				}else{					
				  $consimg = asset('/resources/images/default-chapter-img.png');					
				}				
				
				$consdata=array(
						"id" => $cval->id,
						"class_id" => $cval->class_id,
						"subject_id" => $cval->subject_id,
						"chapter_id" => $cval->chapter_id,
						"name" => $cval->name,
						"description" => htmlentities($cval->description),
						"image" => $consimg,							
						"url" => $cval->url,							
						"learning_outcomes" => htmlentities($cval->learning_outcomes),
						"status" => $cval->status
				  );
					
				  array_push($kconcept,$consdata);	
			    }				
			}
			
			if(!empty($kconcept)){
			  return Response::json(array(
				'status'    => 'success',
				'code'      =>  200,
				'message'   => $kconcept, 
			  ), 200);	
				
			}else{
			  return Response::json(array(
				'status'    => 'error',
				'code'      =>  404,
				'message'   => 'Data not found.', 
			  ), 404);					
			}		 
	}	
	
	public function academicactivity(Request $request){		
				
		$data = Activity::leftJoin('activity_concept','activity_concept.act_id', '=', 'activity.id')			
				->select(['activity.*']);
				
				//activity_technique
				
		if($request->act_id){				
		   
		  $data = $data->where('activity_concept.act_id', $request->act_id);
		}
		
		if(!empty($request->id) || !empty($request->actid)){			
		  $acid=!empty($request->id) ? $request->id : $request->actid;
		  $data = $data->where('activity.id', $acid);
		}		
		
		if($request->class_id){
			
		   $data = $data->where('activity_concept.class_id', '=', $request->class_id );
		}			
		
		if(!empty($request->con_id)){
		
		  $data = $data->where('activity_concept.con_id', $request->con_id);			
		}

		if(!empty($request->subject_id)){
		
		  $data = $data->where('activity_concept.subject_id', $request->subject_id);			
		}

		if(!empty($request->chapter_id)){
		
		  $data = $data->where('activity_concept.chapter_id', $request->chapter_id);
		}							
		 
		$posts=$data->where('activity.status','=','1')->groupBy('activity.id')->get();		
		 
		$word = "wp-content";		 
		$acddata=''; 	 
		$academic=array();
		 
		 if(!empty($posts)){
			  foreach($posts as $cval){	
                //echo "<pre>";print_r($cval); 
				if(strpos($cval->image,$word)!== false){					
                  $mystring=$cval->image;				  
				} else if(file_exists('public/uploads/'.$cval->image)){					
				  $mystring = asset('/public/uploads/'.$cval->image);       
				}else{					
				  $mystring = asset('/resources/images/activity-default-img.png');					
				}				
			
			 
				$actdata=array(
						"id" => $cval->id,
						"teach_id" => $cval->teach_id,
						"title" => $cval->title,
						"url" => $cval->url,
						"description" =>htmlentities($cval->description),
						"learning_outcomes" => htmlentities($cval->learning_outcomes),
						"image" => $mystring,
						"what_you_need" => htmlentities($cval->what_you_need),
						"what_to_do" => htmlentities($cval->what_to_do),
						"change_it" => htmlentities($cval->change_it),
						"coaching" => htmlentities($cval->coaching),
						"game_rules" => htmlentities($cval->game_rules),
						"equipment" => htmlentities($cval->equipment),
						"playing_area" => htmlentities($cval->playing_area),
						"scoring" => htmlentities($cval->scoring),
						"safety" => htmlentities($cval->safety),
						"ask_the_Players" => htmlentities($cval->ask_the_Players),
						"assignments" => htmlentities($cval->assignments),
						"projects" => htmlentities($cval->projects),						
						"status" => $cval->status
					);
					
				  array_push($academic,$actdata);	
			    }				
			}	 
		
		  if(!empty($academic)){
			  return Response::json(array(
				'status'    => 'success',
				'code'      =>  200,
				'message'   => $academic, 
			  ), 200);	
				
			}else{
			  return Response::json(array(
				'status'    => 'error',
				'code'      =>  404,
				'message'   => 'Data not found.', 
			  ), 404);					
			}		  		
    }
	
    public function sportsactivity(Request $request){
        	// dd($request->all());
		$data = Activity::leftJoin('activity_technique','activity_technique.act_id', '=', 'activity.id')
		->join('sports','sports.id','=','activity_technique.sportskill_id')	
		->join('skillareas','skillareas.id','=','activity_technique.skillarea_id')	
		->WhereIn('activity_technique.skillarea_id', ['1', '2'])		
		->select(['activity.*','sports.name', 'skillareas.name as skillareaname'])
		//->select(['activity.*','sports.name'])
	    ->orderBy('activity.id', 'DESC');


				
		if($request->act_id){				
		   
		  $data = $data->where('activity_technique.act_id', $request->act_id);

		}
		
		if(!empty($request->id) || !empty($request->actid)){			
		  $acid=!empty($request->id) ? $request->id : $request->actid;
		  $data = $data->where('activity.id', $acid);

		  


		}		
				
		if($request->class_id){
			
		   $data = $data->where('activity_technique.class_id', '=', $request->class_id );
		}			
		
		if(!empty($request->skillarea)){
		  
		  $data = $data->where('activity_technique.skillarea_id', $request->skillarea);			
		}

		if(!empty($request->technique)){
	
		  $data = $data->where('activity_technique.technique_id', $request->technique);			
		}

		if(!empty($request->skillsports)){
		
		  $data = $data->where('activity_technique.sportskill_id', $request->skillsports);
		}	  
			  
		//$posts=$data->where('activity.status','=','1')->orderBy('title','ASC')->groupBy('activity.id')->get();	
      //  $posts=$data->where('activity.status','=','1')->orderBy('title','ASC')->get();		
        $posts=$data->where('activity.status','=','1')->orderBy('title','ASC')->get();		
		  
		$word = "wp-content";		
			 
		$sport=array();

		 
		 if(!empty($posts)){
			  foreach($posts as $cval){	
                //echo "<pre>";print_r($cval); 
				if(strpos($cval->image,$word)!== false){					
                  $mystring=$cval->image;				  
				} else if(file_exists('public/uploads/'.$cval->image)){					
				  $mystring = asset('/public/uploads/'.$cval->image);       
				}else{					
				  $mystring = asset('/resources/images/activity-default-img.png');					
				}
				
				//if(file_exists('public/uploads/'.$cval->image)){					
				  //$mystring = asset('/public/uploads/'.$cval->image);       
				//}else{					
				  //$mystring = asset('/resources/images/activity-default-img.png');					
				//}

               	$sptdata=array(
						"id" => $cval->id,
						"teach_id" => $cval->teach_id,
						"title" => $cval->title,
						"url" => $cval->url,
						"description" =>"$cval->description",
						"learning_outcomes" =>"$cval->learning_outcomes",
						"image" => $mystring,
						"what_you_need" => "$cval->what_you_need",
						"what_to_do" => "$cval->what_to_do",
						"change_it" => $cval->change_it,
						"coaching" => $cval->coaching,
						"game_rules" => $cval->game_rules,
						"equipment" => $cval->equipment,
						"playing_area" => $cval->playing_area,
						"scoring" => $cval->scoring,
						"safety" => $cval->safety,
						"ask_the_Players" => $cval->ask_the_Players,
						"assignments" => $cval->assignments,
						"projects" => $cval->projects,						
						"status" => $cval->status,
						"SportName"=> $cval->name,
						"SkillArea"=> $cval->skillareaname
						
						
						
					);
					
				  array_push($sport,$sptdata);	
			    }				
			}

			if(!empty($sport)){
			  return Response::json(array(
				'status'    => 'success',
				'code'      =>  200,
				'message'   => $sport, 
			  ), 200);	
				
			}else{
			  return Response::json(array(
				'status'    => 'error',
				'code'      =>  404,
				'message'   => 'Data not found.', 
			  ), 404);					
			}		  		
    }
	
	
	/* public function getconcept(Request $request){      
		//DB::enableQueryLog();		 
		//dd(DB::getQueryLog());		
		$chapter=array();		
        $concdata ='';		
		 
         $data = DB::table('chapter') 					
				 ->select(['chapter.id','chapter.class_id','chapter.subject_id','chapter.name','chapter.description','chapter.image','chapter.url','chapter.learning_outcomes','chapter.unit','chapter.book','chapter.order','chapter.status']);
						 
			if($request->class_id){				
			   $data = $data->where('chapter.class_id', '=', $request->class_id );
			}
			
			if($request->subject_id){
			   $data = $data->where('chapter.subject_id', '=', $request->subject_id );			  
			}		
						
			$chpterdata=$data->where('chapter.status','=','1')->get();	
			
			$chap='';
			$conap='';
			$act_con ='';
			
			if(!empty($chpterdata)){
			  foreach($chpterdata as $cval){				 
				$concdata = DB::table('concept')->where('chapter_id',$cval->id)->get();				
				if(!empty($concdata)){
			      foreach($concdata as $con){
                    if(file_exists('public/uploads/'.$con->image)){					
					  $conimg = asset('/public/uploads/'.$con->image);					  
					}else{					
					  $conimg=asset('/resources/images/default-chapter-img.png');					
					}
					
					$actdata = DB::table('activity_concept')
							->leftJoin('activity','activity_concept.act_id', '=', 'activity.id')
							->where('activity_concept.con_id', $con->id)
							->select(
							  DB::raw("activity_concept.act_id,activity_concept.con_id,activity.title,activity.image,activity.status")
							)->get();
					  
					if(!empty($actdata)){
			           foreach($actdata as $act){						   
						 //echo "<pre>";print_r($act);
	                       $act_con=array(
							"id" => $act->act_id,
							"con_id" => $act->con_id,
							"title" => $act->title,
							"image" => $act->image,							
							"act_status" => $act->status
						  );
                       }				
				    }
							
					$conap=array(
						"id" => $con->id,
						"class_id" => $con->class_id,
						"subject_id" => $con->subject_id,
						"chapter_id" => $con->chapter_id,
						"name" => $con->name,
						"description" => $con->description,
						"learning_outcomes" => $con->learning_outcomes,
						"image" => $conimg,
						"url" => $con->url,
						"con_status" => $cval->status,
						"activity" =>$act_con,
					  );
				   }				
				}				

				if(file_exists('public/uploads/'.$cval->image)){					
				  $chapimg= asset('/public/uploads/'.$cval->image);     
				}else{					
				  $chapimg=asset('/resources/images/default-chapter-img.png');				
				}				
				
				$chap=array(
						"id" => $cval->id,
						"class_id" => $cval->class_id,
						"subject_id" => $cval->name,
						"name" => $cval->name,
						"description" => $cval->description,
						"image" => $chapimg,
						"url" => $cval->url,
						"learning_outcomes" => $cval->learning_outcomes,
						"unit" => $cval->unit,
						"book" => $cval->book,
						"order" => $cval->order,
						"chp_status" => $cval->status,
						"concept" => $conap,						
					);
					
				  array_push($chapter,$chap);				
			   }				
			} 

           //echo "<pre>";print_r($chapter);	
		   //die;		
			return Response::json(array(
			  'status'=>'success',
			  'code'  =>200,
			  'message'=>$chapter
			), 200);		
    } */
}
