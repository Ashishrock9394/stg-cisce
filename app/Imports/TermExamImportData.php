<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Models\Sstudent;
use App\Models\SkillReportSkillTypeTermTypeMapping;
use Illuminate\Support\Facades\DB;


class TermExamImportData implements ToCollection
{
    /**
    * @param Collection $collection
    */
	
	
	public function array(array $array)
    {
	
		die("----change the detail of india");
        $headers = [];
        foreach ($array[0] as $index => $header) 
		{
            $headers[$index] = $header . ' ' . $array[1][$index]; // Concatenate first and second rows
        }

        // Now you can process the data rows with your combined headers
        for ($i = 2; $i < count($array); $i++) 
		{
            $dataRow = array_combine($headers, $array[$i]);
            // Process the data row
            // e.g., store in database
			echo "<pre>";
			print_r($dataRow);
			echo "------bhawanidddddddd-----------";
			
			//print_r($collection->first()->keys()->toArray());
        }
		
		 die('---reach here for payment detail---');
		
    }
	
	public function headingRow(): int
	{
		// If the header is in row 1 and 2
		return 1;
	}
	
	
    public function collection_bk(Collection $collection)
    {

		/*foreach($collection as $key => $val)
		{
			if ($key == 0 || $key == 1 ||  empty($val[0]) || $val[0] != '142913') 
			{
				continue;
			}
			echo "<pre>";
			print_r($val);
			
			
		}
		die('---');*/
		
		
		

		
		// Initialize an empty array to hold the dynamic arrays
		$dataToInsert = [];
		$studentnotexist = [];
		
		 // Define a mapping of column index to skill_type_id
		$skillTypeMapping = 
		[
		7  => 1,
		8  => 2,
		9  => 3,
		10 => 4,
		11 => 5,
		12 => 6,
		13 => 7,
		14 => 8,
		15 => 9,
		16 => 10,
		17 => 11,
		18 => 12,
		19 => 13,
		20 => 14,
		21 => 15,
		22 => 16,
		23 => 17
		];

		
		// Create an array for skill_report_id 1, 2, 3 and 4 etc. based on given data
		$skillReportId = 1;
		
		
		foreach($collection as $key => $val)
		{
			// Skip the first two rows and continue if school code is not '142913'
			if ($key == 0 || $key == 1 ||  empty($val[0]) || $val[0] != '142913') 
			{
				continue;
			}
			
			$schoolCode  = $val[0];
			$studentCode = $val[1];
			

			
			
			$ranges = [
			1 => range(7, 11),
			2 => range(12, 16),
			3 => range(17, 20),
			4 => range(21, 23)
			];
			
			// Define a range for each skill_report_id (e.g., 7 to 11 for skill_report_id 1, etc.)
			foreach ($ranges as $skillReportId => $indexes) 
			{
	
				// Loop through each range for each skill_report_id
				foreach ($indexes as $index => $colIndex) 
				{
				
						$value = $val[$colIndex];  // Get value from collection based on the column index
						$skill_type_id = isset($skillTypeMapping[$colIndex]) ? $skillTypeMapping[$colIndex] : null;


						// Check if the student already exists
						$existingStudent = Sstudent::where('school_code', $val[0])
						->where('student_uid', $val[1])
						->first();

						
					
				$existingMapping = DB::table('skillreport_skilltype_termtype_mapping')
				->where('skill_type_id', $skill_type_id)
				->where('student_id', $existingStudent->id)
				->first();
				
				//echo '---'.$existingStudent->id.'---';
				//echo "<hr>";
				
				
				if (!empty($existingMapping))
				{
			  
					$idd = $existingMapping->id;
					
					
					$sql = "UPDATE skillreport_skilltype_termtype_mapping SET skill_type_value2 ='$value', term_type2_id= 2 WHERE id =".$existingMapping->id;
					echo $sql.';';
					echo "<hr>";

					/*	DB::table('skillreport_skilltype_termtype_mapping')
					->where('id', $existingMapping->id)
					->update([
					'skill_type_value2' => 'z',
					'term_type2_id'     => 2,
					'updated_at'       => now(),
					]);*/

					/*$dat = SkillReportSkillTypeTermTypeMapping::where('id', $idd)
					->update([
					'skill_type_value2' => 'z',
					'term_type2_id'     => 2,
					//'updated_at'       => now(),
					]);*/






					
				}
				else 
				{
					echo "<pre>";
					echo "No matching record found for skill_type_id: $skill_type_id and student_id: {$existingStudent->id}";
					echo "<hr>";
					//die('----');


					// Insert new record if it doesn't exist
					$dataToInsert[] = [
					'school_id'        => $existingStudent->school_id,
					'student_id'       => $existingStudent->id,
					'skill_report_id'  => $skillReportId,
					'skill_type_id'    => $skill_type_id,
					'skill_type_value2' => "$value",
					'term_type2_id'     => 2, // Adjust logic if needed
					'created_at'       => "now()",
					'updated_at'       => "now()",
					];
					
					//print_r($dataToInsert);

					//DB::table('skillreport_skilltype_termtype_mappingd')->insert($dataToInsert);

			  }
						
						
						
					
							
						// Update existing record
						/*DB::table('skillreport_skilltype_termtype_mapping')
						->where('skill_type_id', $skill_type_id)
						->where('student_id', $existingStudent->id)
						->update([
							//'school_id'        => $existingStudent->school_id,
							//'skill_report_id'  => $skillReportId,
							'skill_type_value2' => $value,
							'term_type2_id'     => 2, // Adjust if needed
							'updated_at'       => now(), // Update timestamp
						]);*/
							
					}
				}
			
			// Return success message
			#return response()->json(['message' => 'Data inserted successfully']);

			
		}
		
		
	echo "<pre>";
	echo "------not existing student:-";
	//print_r($dataToInsert);
	echo "<hr>";
	echo "---upated query----";
	//print_r($dataToInsert);

		// Insert the dynamically created arrays into the database
	//DB::table('skillreport_skilltypse_termtype_mapping')->insert($dataToInsert);
	die('---final finish---');

		/**/

		// Return success message
		return response()->json(['message' => 'Data upated successfully']);
		
		#}
		
		
		//echo "<pre>";
		//print_r($collection);
        // die('---reach here for payment detail---');
	    // $this->headings();
    }
	
	public function headings(): array
    {
		echo "<pre>";
		die("------------------");
		print_r($this->collection()->first()->keys()->toArray());
        die('---reach here for payment detail---');
		
        return $this->collection()->first()->keys()->toArray();
    }










    public function collection(Collection $collection) {


    	// DB::table('skillreport_skilltype_termtype_mapping_updated')
        // ->where('student_id', 43)
        // ->update([
        //     'skill_type_value2' =>'X',
        //     'term_type2_id' => 2,
        //     'updated_at' => now(),
        // ]);

        // DB::commit();

        // exit();


		$dataToInsert = [];
		$studentnotexist = [];
		
		$skillTypeMapping = [
		    7  => 1, 8  => 2, 9  => 3, 10 => 4, 11 => 5,
		    12 => 6, 13 => 7, 14 => 8, 15 => 9, 16 => 10,
		    17 => 11, 18 => 12, 19 => 13, 20 => 14, 21 => 15,
		    22 => 16, 23 => 17
		];

		$skillReportId = 1;
			
		foreach($collection as $key => $val) {
			
			if ($key == 0 || $key == 1 ||  empty($val[0]) || $val[0] != '142913')  {
				continue;
			}
			
			$schoolCode  = $val[0];
			$studentCode = $val[1];
			
			$ranges = [
				1 => range(7, 11),
				2 => range(12, 16),
				3 => range(17, 20),
				4 => range(21, 23)
			];
						
			foreach ($ranges as $skillReportId => $indexes) {

				foreach ($indexes as $index => $colIndex) {
					

					$value = $val[$colIndex];
					$skill_type_id = isset($skillTypeMapping[$colIndex]) ? $skillTypeMapping[$colIndex] : null;
					$existingStudent = Sstudent::where('school_code', $schoolCode)
                    ->where('student_uid', $studentCode)
                    ->first();


                    if (!$existingStudent) {
	                    $studentnotexist[] = $studentCode; 
	                    continue; // Skip to the next iteration
	                }

				
					$existingMapping = DB::table('skillreport_skilltype_termtype_mapping_updated')
                    ->where('skill_type_id', $skill_type_id)
                    ->where('student_id', $existingStudent->id)
                    ->first();
					


           
					if (!empty($existingMapping)) {
	                    // Update existing record




	                   $updated = DB::table('skillreport_skilltype_termtype_mapping_updated')
						    ->where('id', $existingMapping->id)
						    ->update([
						        'skill_type_value2' => $value,
						        'term_type2_id' => 2,
						        'updated_at' => now(),
						    ]);

						dd($updated); 


                       //DB::commit();

	                } else {

	                    // Insert new record if it doesn't exist


	                    $dataToInsert[] = [
	                        'school_id'        => $existingStudent->school_id,
	                        'student_id'       => $existingStudent->id,
	                        'skill_report_id'  => $skillReportId,
	                        'skill_type_id'    => $skill_type_id,
	                        'skill_type_value2' => "$value",
	                        'term_type2_id'     => 2, // Adjust logic if needed
	                        'created_at'       => now(), // Use Carbon's now() for timestamp
	                        'updated_at'       => now(), // Use Carbon's now() for timestamp
	                    ];
	                }
				}
			}
		}
		
		
		echo "<pre>"; echo 'Data upated successfully';

		exit();


		return response()->json(['message' => 'Data upated successfully']);

	}
}
