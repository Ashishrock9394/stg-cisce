<?php


namespace App\Helpers;
use DB;
use Session;
use Auth;

class Helper
{
    public static function customSection($custom_class_id)
    {
		return DB::table('custom_classes')->where('id', $custom_class_id)->value('section'); 
    }
	
	 public static function LastTwoDates()
	 {
		$lastTwoDates = DB::table('reports')->select('date')->distinct()->orderBy('date', 'desc')->limit(2)->pluck('date');
		return $lastTwoDates;
	 }
	 
	/**
	 * 05-08-2024
	 * */
	 
	public static function className($class_id)
    {
        return DB::table('class')->where('id', $class_id)->value('name');
    }
	
	public static function changeToRoman($string) {
    
	    if(str_starts_with($string, 'Class')){
	        $number = trim($string , 'Class');
	    }else{
	        return $string;        
	    }

	    $map = array('X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
	    $returnValue = '';
	    while ($number > 0) {
	        foreach ($map as $roman => $int) {
	            if($number >= $int) {
	                $number -= $int;
	                $returnValue .= $roman;
	                break;
	            }
	        }
	    }
	    return $returnValue;
	}
	
	public static function GetSchoolLogo() {
		$schoolId = null;
		if(auth()->guard('web')->check()){
			$user = Auth::user();
			$userRole = $user->role_id;

			if ($userRole == 4 || $userRole == 8) {
				$schoolId = DB::table('school_reference')->where('school_user_id', $user->id)->where('status', 1)->value('school_id');
				
			} elseif ($userRole == 3) {  //Trainer can be belongs to multiple schools.

				if (Session::has('SelectSchoolId')) {
					$schoolId = Session::get('SelectSchoolId');
				} else {
					$schoolId = DB::table('school_trainers')->where('trainer_id', $user->id)->where('status', 1)->value('school_id');
				}

			} elseif ($userRole == 9 || $userRole == 2) {
				return (object)[
					'school_name' => $user->name,
					'logo'        => null,
					'school_code' => null,
				];
			}
		}
	    elseif(auth()->guard('sstudent')->check()){
			$schoolId = auth()->guard('sstudent')->user()->school_id;
		}

	    if ($schoolId) {
	        return DB::table('schools')->select('school_name', 'logo', 'school_code')->where('id', $schoolId)->first();
	    }

	    return null;
	}

	public static function resizeAndSaveImage($file, $height, $folder, $id)
	{
		$path = public_path("assets/uploads/{$folder}/");
		$timestamp = \Carbon\Carbon::now()->format('Ymd_His');
		$type = @exif_imagetype($file->getRealPath());

		if ($type === false) {
			throw new \Exception('Invalid image file.');
		}
		switch ($type) {
			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($file->getRealPath());
				$extension = 'jpg';
				break;

			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($file->getRealPath());
				$extension = 'png';
				break;

			case IMAGETYPE_WEBP:
				$src = imagecreatefromwebp($file->getRealPath());
				$extension = 'webp';
				break;

			default:
				throw new \Exception('Unsupported image type.');
		}

		if (!$src) {
			throw new \Exception('Unable to create image resource.');
		}

		$filename = $id . '_picture_' . $timestamp . '.' . $extension;
		$destination = $path . $filename;

		$origWidth = imagesx($src);
		$origHeight = imagesy($src);

		if ($origWidth <= 0 || $origHeight <= 0) {
			imagedestroy($src);
			throw new \Exception('Invalid image dimensions.');
		}

		$width = intval(($height / $origHeight) * $origWidth);

		$dst = imagecreatetruecolor($width, $height);

		if (!$dst) {
			imagedestroy($src);
			throw new \Exception('Unable to create destination image.');
		}

		if (in_array($extension, ['png', 'webp'])) {
			imagealphablending($dst, false);
			imagesavealpha($dst, true);
			$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
			imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);
		}
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

		// Save image
		switch ($extension) {
			case 'jpg':
				imagejpeg($dst, $destination, 80);
				break;

			case 'png':
				imagepng($dst, $destination, 6);
				break;

			case 'webp':
				imagewebp($dst, $destination, 80);
				break;
		}

		imagedestroy($src);
		imagedestroy($dst);

		return $filename;
	}
		
	public static function ClassSectionName($customId) {
		$classes = DB::table('custom_classes')
		->join('class', 'class.id', '=', 'custom_classes.class_id')
		->select('class.name','custom_classes.section')
		->where('custom_classes.id', $customId)
		->where('custom_classes.status', '1')
		->orderBy('custom_classes.orders', 'ASC')
		->first();
		
		return $classes;
    }

    public static function getClassAndSection($custom_class_id) {


    	$classData = DB::table('custom_classes')
	        ->join('class', 'class.id', '=', 'custom_classes.class_id')
	        ->where('custom_classes.id', $custom_class_id)
	        ->select(
	            'custom_classes.section',
	            DB::raw("CASE 
	                        WHEN custom_classes.nomenclature IS NOT NULL AND custom_classes.nomenclature <> '' 
	                        THEN custom_classes.nomenclature 
	                        ELSE class.name 
	                    END AS class_name")
	        )
	        ->first();

	    if ($classData) {
	        return $classData->class_name . ' - ' . $classData->section;
	    }

	    return null;

	   
    }

    public static function getSkillArea($skill_area_id){
    	return DB::table('skillareas')->where('id',$skill_area_id)->value('name');
    }

    public static function getSports($skillsports_id){
    	return DB::table('sports')->where('id',$skillsports_id)->value('name');
    }

    public static function getTechnique($technique_id){
    	return DB::table('techniques')->where('id',$technique_id)->value('name');
    }

    public static function getActivity($activity_id){
    	return DB::table('activity')->where('id',$activity_id)->value('title');
    }

    public static function getOtherDuties($other_duties_id){
    	return DB::table('trainer_other_duties')->where('id',$other_duties_id)->value('name');
    }


}