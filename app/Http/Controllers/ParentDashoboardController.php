<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentDashboard;
use App\Models\ClassModel;
use App\Models\StudentInfo;
use App\Models\Food;
use App\Models\FoodItems;
use Carbon\Carbon;
use App\Models\WaterIntake;
use App\Models\Report;
use App\Models\Activity;
use App\Models\SleepRecord;
use App\Models\CalorieTarget;
use Illuminate\Support\Facades\DB;
 

class ParentDashoboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
		$this->middleware('auth:sstudent');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {        
	
        $student = $studentId = Auth::guard('sstudent')->user();
        $student_id = $studentId->id;
		

        // - Animish
        // no. of pe classes
        $currentDate = Carbon::now()->format('Y/m/d');

        $dateInput = $request->input('date', $currentDate);

        $selectedDatePE = Carbon::createFromFormat('Y/m/d', $dateInput);
        $pastDate = $selectedDatePE->copy()->subDays(7);

        $reports = Report::where('student_id', $student_id)
            ->whereBetween('date', [$pastDate->format('Y/m/d'), $selectedDatePE->format('Y/m/d')])
            ->get();
			
			
		 		
        // $fromdate=$pastDate->format('d/m/Y');
        // $todate=$selectedDatePE->format('d/m/Y');

        // $fromdate='05/04/2024';
        // $todate='30/04/2024';


        // $reports = Report::where('student_id', $student_id)
        // ->whereBetween(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), [DB::raw("STR_TO_DATE('$fromdate', '%d/%m/%Y')"), DB::raw("STR_TO_DATE('$todate', '%d/%m/%Y')")])
        // ->get();

        $activities = [];

        foreach ($reports as $report) {
            $activity_id = $report->activity_id;

            $activity = Activity::find($activity_id);

            if ($activity) {
                $activities[] = $activity;
            }
        }


        // - Piyush
        // my profile, and bmi
        $class = $student->class;
		
		 #echo "<pre>";
		 #print_r($student);
		 #die('----change the detail------');
		
		
        $customClass = $student->customClass;
		
		
		
		
	

        $studentInfo = StudentInfo::where('student_id', $student->id)->latest()->first();

        $targetBmi = 22;
        $targetWeight = null;

        if(!$studentInfo){
            session(['showEditProfileModal' => true]);
            $data = [
                'name' => $student->student_name,
                'class_name' => $class->name,
                'section' => $customClass->section,
                'age' => null,
                'height' => null,
                'weight' => null,
                'bmi' => null,
                'target_weight' => null
            ];
        }
        else {
            session()->forget('showEditProfileModal');
            $targetWeight = $targetBmi * pow($studentInfo->height / 100, 2); 

            $data = [
                'name' => $student->student_name,
                'class_name' => $class->name,
                'section' => $customClass->section,
                'age' => $studentInfo->age,
                'height' => $studentInfo->height,
                'weight' => $studentInfo->weight,
                'bmi' => $studentInfo->bmi,
                'target_weight' => $targetWeight
            ];
            
        }


        // - Dhruv
        // calorie intake
        $typeFood = '';
        $currentTime = Carbon::now();
        $time = $currentTime->format('H:i');
       
        $selectedDateFood = Carbon::today()->toDateString();
        $calorieRecords = FoodItems::where('user_id', $student->id)
            ->whereDate('created_at', $selectedDateFood)
            ->with('food') 
            ->get();

        $totalCalories = [];
        $calories = [
            'breakfast' => 0,
            'lunch' => 0,
            'snacks' => 0,
            'dinner' => 0,
        ];

        $mealDetails = [
            'breakfast' => [],
            'lunch' => [],
            'snacks' => [],
            'dinner' => [],
        ];

        $totalCaloriesAllToday = 0;

        foreach ($calorieRecords as $record) {
            $totalCaloriesForFood = $record->calculated_calories;
            $totalCaloriesAllToday += $totalCaloriesForFood;

            switch ($record->status) {
                case 1:
                    $calories['breakfast'] += $totalCaloriesForFood;
                    $mealDetails['breakfast'][] = [
                        'id' => $record->id,
                        'name' => $record->food_name,
                        'calories' => $totalCaloriesForFood,
                        'time' => $record->created_at->format('H:i'),
						'amount'=>$record->amount,
						'serving_type'=>$record->serving_type
                    ];
                    break;
                case 2:
                    $calories['lunch'] += $totalCaloriesForFood;
                    $mealDetails['lunch'][] = [
                        'id' => $record->id,
                        'name' => $record->food_name,
                        'calories' => $totalCaloriesForFood,
						'amount'=>$record->amount,
						'serving_type'=>$record->serving_type,
                        'time' => $record->created_at->format('H:i')
                    ];
                    break;
                case 3:
                    $calories['snacks'] += $totalCaloriesForFood;
                    $mealDetails['snacks'][] = [
                        'id' => $record->id,
                        'name' => $record->food_name,
                        'calories' => $totalCaloriesForFood,
						'amount'=>$record->amount,
						'serving_type'=>$record->serving_type,
                        'time' => $record->created_at->format('H:i')
                    ];
                    break;
                case 4:
                    $calories['dinner'] += $totalCaloriesForFood;
                    $mealDetails['dinner'][] = [
                        'id' => $record->id,
                        'name' => $record->food_name,
                        'calories' => $totalCaloriesForFood,
						'amount'=>$record->amount,
						'serving_type'=>$record->serving_type,
                        'time' => $record->created_at->format('H:i')
                    ];
                    break;
            }
        }

        $totalCaloriesByDay = [];
        $calorieRecordHistory = FoodItems::where('user_id', $student->id)->with('food')->get();
        foreach ($calorieRecordHistory as $record) {
            $dateFood = $record->created_at->toDateString();

            if (!isset($totalCaloriesByDay[$dateFood])) {
                $totalCaloriesByDay[$dateFood] = 0;
            }

            $totalCaloriesForFood = $record->calculated_calories;
            $totalCaloriesByDay[$dateFood] += $totalCaloriesForFood;
        }

        $totalCaloriesAll = array_sum($totalCaloriesByDay);


        $foodItems = Food::all();


        // - Himanshu
        // water intake
        $today = Carbon::today();
        $studentId = Auth::id();
        $waterIntake = WaterIntake::where('user_id', $studentId)
            ->whereDate('date', $today)
            ->sum('amount');

        $dailyGoal = 8; 
        $percentageOfGoal = ($waterIntake / $dailyGoal) * 100;

        $calorieTarget = CalorieTarget::where('user_id', $student->id)->first();
        $targetCalories = $calorieTarget ? $calorieTarget->target_calories:2000;


        // - Ayushman
        // sleep record
        $sleepRecords = SleepRecord::where('student_id', $studentId)->whereDate('sleep_date', today())->get();
    
        $totalSleepHours = 0;
        foreach ($sleepRecords as $record) {
            $totalSleepHours += $record->calculateTotalSleepHours();
        }
    
        $sleepGoal = 8;
        $sleepGoalPercentage = ($totalSleepHours / $sleepGoal) * 100;
    
        $sleepHistory = SleepRecord::where('student_id', $studentId)->orderBy('sleep_date', 'desc')->get();
    
        $history = [];
        foreach ($sleepHistory as $record) {
            $totalSleepHoursHistory = $record->calculateTotalSleepHours();
            $sleepGoalPercentageHistory = ($totalSleepHoursHistory / 8) * 100;
            $history[] = [
                'date' => $record->sleep_date,
                'totalSleepHours' => $totalSleepHoursHistory,
                'sleepGoalPercentage' => $sleepGoalPercentageHistory
            ];
        }


        return view('ParentDashboard', compact('data','reports','activities','totalSleepHours','sleepRecords','sleepGoalPercentage','history','calorieRecords', 'calories', 'totalCalories', 'totalCaloriesAll', 'totalCaloriesAllToday', 'targetCalories', 'totalCaloriesByDay','typeFood','waterIntake','percentageOfGoal','foodItems','mealDetails'));
    }

    
    // - Piyush
    public function updateProfile(Request $request){

        $request->validate([
            'age' => 'required|integer|min:1|max:120',
            'height' => 'required|numeric|min:1|max:999.99',
            'weight' => 'required|numeric|min:1|max:999.9',
        ]);

       # $student = Auth::user();
       # $id = Auth::id();
		
		
		 $student = Auth::guard('sstudent')->user();
         $id = $studentId->id;
		
		
        $studentInfo = $student->studentInfo;

        $height_m = $request->height / 100;
        $bmi = ($height_m > 0) ? round(($request->weight) / ($height_m * $height_m), 2) : 0;

        StudentInfo::create([
            'student_id' => $id,
            'age' => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $bmi
        ]);

        return redirect()->route('parent-dashboard')->with('status', 'Profile updated successfully.');
    }


    // - Dhruv
    public function foodstore(Request $request)
    {
        $validatedData = $request->validate([
            'food' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|integer|in:1,2,3,4',
        ]);
		
		$user = Auth::guard('sstudent')->user();
        //$id = $studentId->id;


        #$user = Auth::user();
		
		 	
        $foodItem = Food::where('food_item_name', $request->food)->first();

        if (!$foodItem) {
            return redirect()->back()->withErrors(['food' => 'Food item not found']);
        }

        $calorieRecord = new FoodItems();
        $calorieRecord->user_id = $user->id;
        $calorieRecord->food_id = $foodItem->id; 
        $calorieRecord->food_name = $request->food;
        $calorieRecord->amount = $request->amount;
        $calorieRecord->status = $request->status; 
        $calorieRecord->serving_type = $foodItem->serving_unit; 
        $calorieRecord->save();

        return redirect()->back()->with('success', 'Calorie record saved successfully!');
        }

    // - Dhruv
    public function deleteMeal($id)
    {
        #$userId = Auth::id();
		$user    = Auth::guard('sstudent')->user();
         $userId = $user->id;

        $foodItem = FoodItems::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($foodItem) {
            $foodItem->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Meal item not found or unauthorized.'], 404);
        }
    }

    // - Dhruv
    public function setCalorieTarget(Request $request)
    {
        $request->validate([
            'target_calories' => 'required|integer|min:0',
        ]);

        #$user = Auth::user();
		$user    = Auth::guard('sstudent')->user();
        #$userId = $user->id;

        $targetCalories = CalorieTarget::updateOrCreate(
            ['user_id' => $user->id],
            ['target_calories' => $request->input('target_calories')]
        );

        return redirect()->back()->with('success', 'Calorie target updated successfully!');
    }


    // - Ayushman
    public function storeDaySleep(Request $request)
    {
        $daySleep = new SleepRecord();
        $daySleep->day_sleep_start = $request->day_sleep_start;
        $daySleep->day_sleep_end = $request->day_sleep_end;
        $daySleep->sleep_date = $request->sleep_date;
        $daySleep->student_id = Auth::id(); 

        $daySleep->save();

        return redirect()->back()->with('success', 'Day sleep record added successfully.');
    }

    
    // - Ayushman
    public function getTotalSleepHoursForToday()
    {
        $today = Carbon::now()->toDateString();

        $daySleepHours = SleepRecord::whereDate('sleep_date', $today)->sum('day_sleep_hours');
        $nightSleepHours = SleepRecord::whereDate('sleep_date', $today)->sum('night_sleep_hours');

        return $daySleepHours + $nightSleepHours;
    }

    
    // - Ayushman
    public function storeNightSleep(Request $request)
    {
        $nightSleep = new SleepRecord();
        $nightSleep->night_sleep_start = $request->night_sleep_start;
        $nightSleep->night_sleep_end = $request->night_sleep_end;
        $nightSleep->sleep_date = $request->sleep_date_night;
        $nightSleep->student_id = Auth::id(); 

        $nightSleep->save();

        return redirect()->back()->with('success', 'Night sleep record added successfully.');
    }


    // - Ayushman
    public function sleepHistory()
    {
        #$userId = Auth::id();
		
		$user    = Auth::guard('sstudent')->user();
        $userId  = $user->id;
		
        $sleepRecords = SleepRecord::where('student_id', $userId)->orderBy('sleep_date', 'desc')->get();

        $history = [];
        foreach ($sleepRecords as $record) {
            $totalSleepHours = $record->calculateTotalSleepHours();
            $sleepGoalPercentage = ($totalSleepHours / 8) * 100;
            $history[] = [
                'date' => $record->sleep_date,
                'totalSleepHours' => $totalSleepHours,
                'sleepGoalPercentage' => $sleepGoalPercentage
            ];
        }

        return view('history', compact('history'));
    }


    // - Himanshu
    public function showByDate(Request $request)
    {
        $selectedDate = Carbon::parse($request->selected_date);
        #$userId = Auth::id();
		
	     $user    = Auth::guard('sstudent')->user();
         $userId  = $user->id;
		
        $waterIntake = WaterIntake::where('user_id', $userId)
            ->whereDate('date', $selectedDate)
            ->sum('amount');

        $dailyGoal = 8; 
        $percentageOfGoal = ($waterIntake / $dailyGoal) * 100;

        return response()->json([
            'waterIntake' => $waterIntake,
            'percentageOfGoal' => $percentageOfGoal,
            'selectedDate' => $selectedDate->toFormattedDateString(),
        ]);
    }

    // - Himanshu
    public function waterstore(Request $request)
    {
        $request->validate([
            'water_intake' => 'required|integer|min:1|max:30',
            'date' => 'nullable|date'
        ]);
    
        $date = $request->input('date') ?? now()->toDateString();
		
		
		
		$user    = Auth::guard('sstudent')->user();
		$userId  = $user->id;
    
        WaterIntake::create([
            'user_id' => $userId,
            'amount' => $request->water_intake,
            'date' => $date,
        ]);
    
        return redirect('/parent-dashboard')->with('status', 'Water intake added successfully!');
    
    }


    public function logout(Request $request) {
		
		#$user    = Auth::guard('sstudent')->user();
		#$userId  = $user->id;
		
		
		$std = Auth::guard('sstudent')->user(); 
		$this->guard('sstudent')->logout();
			
       // Auth::logout();
        return redirect('/login');
        
    }

    
}
