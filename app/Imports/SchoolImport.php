<?php

namespace App\Imports;

use App\Models\School;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;


class SchoolImport implements ToCollection, withHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
	protected $chainopts;
	public function __construct($chainopts)
	{
		$this->chainopts = $chainopts;
	}
	
	public function collection (Collection $rows)
	{
		
		$rows->each(function($row, $key){
		
	
		 if(!empty($row)){
			 //dd($row);
		  					
		  $count = School::where('school_name', $row['school_name'])->count();
			if($count == 0){	
			 School::create([
				'chain_id' => $this->chainopts,
				'school_name' => $row['school_name'],
				'school_principal' => $row['school_principal'],
				'school_email' => $row['school_email'],
				'principal_phone' => $row['principal_phone'],
				'city' => $row['city'],
				'state' => $row['state'],	
				'district' => $row['district'],
				'region' => $row['region'],
				'zonename' => $row['zonename'],
				'address' => $row['address'],
				'chain' => $row['chain'],
				'board' => $row['board'],
				'pincode' => $row['pincode'],
				'region_id' => $row['region_id'],
			
	
			
			]);
		}
	}
	});
	}
}
