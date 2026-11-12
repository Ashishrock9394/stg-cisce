<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request,Response,Redirect;
use App\Models\User;
use App\Models\Usermeta;

class UserImport implements ToCollection,WithHeadingRow
{
    protected $regions;
	protected $schools;
	protected $roles;
	protected $chain;
	
	public function __construct( $chain, $roles, $regions, $schools  )
	{
		$this->chain = $chain;
		$this->roles = $roles;
		$this->regions = $regions;
		$this->schools = $schools;
		
	}
	
	public function collection (Collection $rows)
	{
			echo '<pre>';
			//print_r( $this->chain );
			//print_r( $this->roles );
			//print_r( $this->regions );
			//print_r( $this->schools );
		
		$rows->each(function($row, $key){
			if(!empty($row)){
				
				
				
				if(!empty($row['designation'])){
					if(in_array( strtolower($row['designation']), $this->roles)){
						$roleid = array_search (strtolower($row['designation']), $this->roles);
					}else{
						$roleid = 3;
					}
				}else{
					$roleid = 3;
				}
				
				
				if(!empty($row['schoolname'])){
					if(in_array( strtolower($row['schoolname']), $this->schools)){
						$schoolid = array_search (strtolower($row['schoolname']), $this->schools);
					}
				}
				
				if(!empty($row['region'])){
					//echo $row['region'];			
					if(in_array( strtolower($row['region']), $this->regions)){
						$regionid = array_search (strtolower($row['region']), $this->regions);
					}
				}
				
				
				
				$user = new User();
				$user->name = $row['name'];
				$user->email =  $row['email'];
				$user->phone =  $row['phone'];
				$user->role_id =  $roleid;
				$user->password =  Hash::make('G4F@2021#');
				
				try {
					$userres = $user->save();
					
				} catch (\Illuminate\Database\QueryException $e) {
					var_dump($e->errorInfo);
				}
				
				if(!empty($userres)){
				if(!empty($user->id)){
					
					$usermeta = new Usermeta();
					$usermeta->user_id = $user->id;
					
										
					if(!empty($schoolid)){
						$usermeta->school_id = $schoolid;
					}
					
					if(!empty($regionid)){
						$usermeta->region_id = $regionid;
					}
					
					if(!empty($row['qualification'])){
						$usermeta->qualification = $row['qualification'];
					}
					if(!empty($row['experience'])){
						$usermeta->experience = $row['experience'];
					}
					
					if(!empty($row['subject'])){
						$usermeta->subject = $row['subject'];
					}
					
					$usermeta->created_by = Auth::user()->id; 
				
					$usermeta->save();
					
				}
				}
				
				
			}
		});
			
		
		
		
				/*
				[name] => Sh. Rajinder Kumar
				[email] => rajin12176@gmail.com
				[phone] => 7973793714
				[designation] => Principal
				[schoolname] => Karbi Anglong - I
				[qualification] => M.A, B. Ed
				[experience] => 
				[subject] => SOCIAL STUDIES  
				[region] => Shillong
				*/
	
    }

}
