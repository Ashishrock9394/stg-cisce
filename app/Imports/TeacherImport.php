<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Usermeta;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class TeacherImport implements ToCollection, withHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
	protected $regions;
	protected $school;
	protected $schoolid;
	protected $schoolname;
	protected $teacher;
	public function __construct($regions,$school)
	{
		$this->regions = $regions;
		$this->school = $school;
		
	}
	
	public function collection (Collection $rows)
	{
		echo "<pre>";
			 
		//print_r($this->school);
		
		
		$rows->each(function($row, $key){
			//$this->schoolid = NULL;
			//$this->schoolname = NULL;
			//$this->teacher = NULL;
	
		 if(!empty($row)){
			// print_r($row);
			 
			 
							
		$uer = new User();
		$uer->name ="Shakti 2";
		$uer->email = "testt@gmail.com";
		$uer->phone =6767676765;
		$uer->role_id = 3;
		$uer->password = Hash::make("Navodaya@2021");
		$uer->save();
		/*
		$this->teacher->name = $row['teacher'];
		$this->teacher->email = $row['email'];
		$this->teacher->phone =  $row['contact'];
		//$this->teacher->role_id = 3;
		//$this->teacher->password = Hash::make('Navodaya@2021');
		
		$this->teacher->save();
		*/
		
/*		
		$teacher = Teacher::create([
			'name' => $row['teacher'],
            'email' => $row['email'],
			'phone' => $row['contact'],
			'role_id' => 3,
            'password' => Hash::make('Navodaya@2021')
			]);	
			
			print_r($this->teacher);
			*/ 
			
		
			
		
		
		
		/*
		if($teacher->id){
			
			$usermeta = new Usermeta();
			$usermeta->user_id = $teacher->id;
			
			 
			if(!empty($row['schoolname'])){
				$this->schoolname = strtolower($row['schoolname']);
				$usermeta->school_name = $this->schoolname;
				
				if(in_array($this->schoolname, $this->school)){
					echo $this->schoolname; 
					$this->schoolid = array_search($this->schoolname, $this->school ) ;
					if(!empty($this->schoolid)){
						$usermeta->school_id = $this->schoolid; 
					}
				}
			}
			
			$usermeta->created_by = Auth::user()->id;
			$usermeta->qualification = $row['qualification'];
			$usermeta->experience = $row['experience'];
			$usermeta->gender = $row['gender'];
			$usermeta->save();
			die();
			exit();
		}
		*/
			
			/*
			
			 [teacher] => Shri A K Gite
            [contact] => 9425955258
            [email] => anilkumargite68@gmail.com
            [gender] => Male
            [experience] => 27 yrs
            [qualification] => MPED
            [schoolname] => Burhanpur
			
			$teacher = Teacher::create([
		
	
			
			]);
			//$teacher->save();
			
			if($teacher->id){
			
				$usermeta = new Usermeta();
				$usermeta->user_id = $teacher->id;
				
				$usermeta->school_name = $row['jnv'];
				$usermeta->school_id = $school->id;
				$usermeta->created_by = Auth::user()->id;
				$usermeta->qualification = $row['qualification'];
				$usermeta->experience = $row['experience'];
				
				
				
				//$usermeta->save();
			}
			
			*/
	die();	
	}
	
	die();
	});
	
	}
}
