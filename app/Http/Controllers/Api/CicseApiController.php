<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Sstudent;
use App\Models\Sclass;
use App\Models\CustomClass;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CicseApiController extends Controller
{

    /**
     * API 1 – Checking if Data is Uploaded for a particular school or not
     * */
    public function checkSchoolData(Request $request) {


        $request->validate([
            'schoolcode' => 'required|string'
        ]);

        $school = School::where('school_code', $request->schoolcode)->first();

        if (!$school) {
            return response()->json(['error' => 'School Code not present in database'], 404);
        }

        $studentCount = Sstudent::where('school_id', $school->id)
            ->where('academic_year','2026-2027')
            ->where('status', 'active')
            ->count();

        return response()->json([
            'schoolcode' => $request->schoolcode,
            'totalstudents' => $studentCount
        ]);
    }


    /**
     * API 2 – Bulk Fetch Student Summary Data for all schools
     * */

    public function fetchAllSchoolSummary(Request $request) {

        $excludedSchoolIds = [3129, 3130, 3131];
        $studentCounts = DB::table('students')->select('school_id', DB::raw('count(*) as totalstudents'))
            ->whereNotIn('school_id', $excludedSchoolIds)
            ->where('academic_year', '2026-2027')
            ->where('status', 'active')
            ->groupBy('school_id')->get()
            ->keyBy('school_id');

        $schools = DB::table('schools')
            ->whereNotIn('id', $excludedSchoolIds)
            ->whereNotNull('school_code')
            ->select('id', 'school_code')
            // ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($schools as $school) {
            $data[] = [
                'schoolcode' => $school->school_code,
                'totalstudents' => $studentCounts[$school->id]->totalstudents ?? 0
            ];
        }

        return response()->json($data);
    }


    /**
     * API 3: Fetch Student Data by Class and Section
     * */
    public function fetchStudentsByClassSection(Request $request) {

        $request->validate([
            'schoolcode' => 'required|string',
            'class' => 'required|string',
            'section' => 'required|string'
        ]);

        $school = School::where('school_code', $request->schoolcode)->first();

        if (!$school) {
            return response()->json([
                'error' => 'School Code not present in database'
            ], 404);
        }

        $className = 'Class ' . trim($request->class);
      


        $class = Sclass::where('name','LIKE', "%$className%")->where('status', 1)->first();
        if (!$class) {
            return response()->json([
                'error' => 'Class does not exist'
            ], 404);
        }
        

        $query = CustomClass::where('class_id', $class->id)->where('school_id', $school->id);
        $customClass = $query->first();

        if(!$customClass){
             return response()->json([
                'error' => 'Class not found'
            ], 404);
        }

        $customClass = $query->where('section', $request->section)->first();
        if (!$customClass) {
            return response()->json([
                'error' => 'Section not found for the given class'
            ], 404);
        }

        $students = Sstudent::where('school_id', $school->id)
            ->where('academic_year','2026-2027')
            ->where('custom_class_id', $customClass->id)
            ->where('status', 'active')
            ->get();


        if ($students->isEmpty()) {
            return response()->json([
                'message' => 'No students found for the given class and section'
            ], 404);
        }

        $studentDetails = $students->map(function ($student, $index) {
            return [
                'SNo' => $index + 1,
                'admissionNo' => $student->student_uid,
                'aadhaar' => $student->adhaarId ?? '',
                'apaar' => $student->apaarId ?? '',
                'passport' => $student->passport ?? '',
                'studentName' => $student->student_name,
                // 'MiddleName' => $student->middle_name ?? '',
                // 'LastName' => $student->last_name,
                'gender' => $student->gender,
                'rollNo' => $student->rollno,
                // 'dob' => $student->dob ? Carbon::parse($student->dob)->format('d-m-Y') : null,
                'dob' => $student->dob
                            ? (Carbon::hasFormat($student->dob, 'd/m/Y')
                                ? Carbon::createFromFormat('d/m/Y', $student->dob)->format('d-m-Y')
                                : (Carbon::hasFormat($student->dob, 'Y-m-d')
                                    ? Carbon::createFromFormat('Y-m-d', $student->dob)->format('d-m-Y')
                                    : null))
                            : null,

            ];
        });

        return response()->json([
            'schoolcode' => $school->school_code,
            'class' => $request->class,
            'section' => $request->section,
            'studentdetails' => $studentDetails
        ]);
    }


    /**
     * Kept Only For Testing Purpose.
     * */


    public function TestfetchAllSchoolSummary(Request $request) {

        // $excludedSchoolIds = [3129, 3130, 3131];
        $studentCounts = DB::table('students')->select('school_id', DB::raw('count(*) as totalstudents'))
            // ->whereNotIn('school_id', $excludedSchoolIds)
            ->where('academic_year', '2026-2027')
            ->where('status', 'active')
            ->groupBy('school_id')->get()
            ->keyBy('school_id');

        $schools = DB::table('schools')
            // ->whereNotIn('id', $excludedSchoolIds)
            ->whereNotNull('school_code')
            ->select('id', 'school_code')
            // ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($schools as $school) {
            $data[] = [
                'schoolcode' => $school->school_code,
                'totalstudents' => $studentCounts[$school->id]->totalstudents ?? 0
            ];
        }

        return response()->json($data);

    }

    // for career portal 

    public function fetchStudentsBySchoolCode(Request $request) {

        $request->validate([
            'schoolcode' => 'required|string'
        ]);

        $school = School::where('school_code', $request->schoolcode)->first();

        if (!$school) {
            return response()->json([
                'status' => 404,
                'message' => 'School Code not present in database',
                'schoolcode' => $request->schoolcode,
            ], 404);
        }

        $students = Sstudent::where('school_id', (string)$school->id)
            ->where('academic_year', '2026-2027')
            ->where('status', 'active')
            ->where('paymentStatus', '!=', 'paid')
            ->get();

        if ($students->isNotEmpty() && $students->first()->batchId) {
            $batchId = $students->first()->batchId;
        } else {
            $batchId = $request->schoolcode . '-' . Str::uuid();
        }

        $time = now('Asia/Kolkata')->toIso8601String();
        
        Sstudent::where('school_id', (string)$school->id)
            ->where('academic_year', '2026-2027')
            ->where('status', 'active')
            ->where('paymentStatus', '!=', 'paid')
            ->update([
                'batchId' => $batchId,
                'paymentStatus' => 'pending',
                'batchCreatedAt' => $time,
            ]);

        $studentCount=$students->count();

        return response()->json([
            'schoolcode' => $request->schoolcode,
            'schoolid' => $school->id,
            'totalstudents' => $studentCount,
            'batchId' => $batchId,
            'batchCreatedAt' => $time,
        ]);
    }

    // update Batch status 
    public function updateBatchStatus(Request $request){

        try {
            $request->validate([
                'schoolcode' => 'required|string',
                'batchId' => 'required|string',
                'batchStatus' => 'required|string|in:Success,Failed,Failure',
                'careersOnlinePaymentID' => 'nullable|string',
                'paymentDateTime' => 'nullable|string',
                'gatewayTxnId' => 'nullable|string',
            ]);

            $school = School::where('school_code', $request->schoolcode)->first();

            if (!$school) {
                return response()->json([
                    'status' => 404,
                    'message' => 'School Code not present in database',
                    'schoolcode' => $request->schoolcode,
                    'batchId' => $request->batchId,
                ], 404);
            }

            $paidOn = $request->filled('paymentDateTime')
                ? Carbon::parse($request->paymentDateTime)->format('Y-m-d H:i:s')
                : null;

            $time = now('Asia/Kolkata')->toIso8601String();

            $students = Sstudent::where('school_id', (string) $school->id)
                ->where('academic_year', '2026-2027')
                ->where('status', 'active')
                ->where('batchId', $request->batchId)
                ->where('paymentStatus', 'pending');

            if (!$students->exists()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Batch ID not found or already processed',
                    'schoolcode' => $request->schoolcode,
                    'batchId' => $request->batchId,
                ], 404);
            }

            $batchStatus = strtolower($request->batchStatus);

            if ($batchStatus === 'success') {

                $students->update([
                    'paymentStatus'     => 'paid',
                    'careersPaymentRef' => $request->careersOnlinePaymentID,
                    'gatewayTxnId'      => $request->gatewayTxnId,
                    'paidOn'            => $paidOn,
                    'updatedOn'         => $time,
                ]);

                DB::table('payment_details')->insert([
                    'school_code'       => $request->schoolcode,
                    'school_name'       => $school->school_name,
                    'batchId'           => $request->batchId
                ]);

            } else {
                $students->update([
                    'batchId'           => null,
                    'batchCreatedAt'    => null,
                    'paymentStatus'     => 'unpaid',
                    'careersPaymentRef' => null,
                    'gatewayTxnId'      => null,
                    'paidOn'            => null,
                    'updatedOn'         => $time,
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Batch status updated successfully.',
                'data' => [
                    'schoolcode' => $request->schoolcode,
                    'batchId' => $request->batchId,
                    'batchStatus' => $batchStatus,
                ]
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {

            \Log::error('Update Batch Status API Error', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error. Please try again later.'
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function TestfetchStudentsByClassSection(Request $request) {

        $request->validate([
            'schoolcode' => 'required|string',
            'class' => 'required|string',
            'section' => 'required|string'
        ]);

        $school = School::where('school_code', $request->schoolcode)->first();

        if (!$school) {
            return response()->json([
                'error' => 'School Code not present in database'
            ], 404);
        }

        $className = 'Class ' . trim($request->class);
      


        $class = Sclass::where('name','LIKE', "%$className%")->where('status', 1)->first();
        if (!$class) {
            return response()->json([
                'error' => 'Class does not exist'
            ], 404);
        }
        

        $query = CustomClass::where('class_id', $class->id)->where('school_id', $school->id);
        $customClass = $query->first();

        if(!$customClass){
             return response()->json([
                'error' => 'Class not found'
            ], 404);
        }

        $customClass = $query->where('section', $request->section)->first();
        if (!$customClass) {
            return response()->json([
                'error' => 'Section not found for the given class'
            ], 404);
        }

        $students = Sstudent::where('school_id', $school->id)
            ->where('custom_class_id', $customClass->id)
            ->where('academic_year','2026-2027')
            ->where('status', 'active')
            ->get();


        if ($students->isEmpty()) {
            return response()->json([
                'message' => 'No students found for the given class and section'
            ], 404);
        }

        $studentDetails = $students->map(function ($student, $index) {
            return [
                'SNo' => $index + 1,
                'admissionNo' => $student->student_uid,
                'aadhaar' => $student->adhaarId ?? '',
                'apaar' => $student->apaarId ?? '',
                'passport' => $student->passport ?? '',
                'studentName' => $student->student_name,
                // 'MiddleName' => $student->middle_name ?? '',
                // 'LastName' => $student->last_name,
                'gender' => $student->gender,
                'rollNo' => $student->rollno,
                // 'dob' => $student->dob ? Carbon::parse($student->dob)->format('d-m-Y') : null,
                'dob' => $student->dob
                            ? (Carbon::hasFormat($student->dob, 'd/m/Y')
                                ? Carbon::createFromFormat('d/m/Y', $student->dob)->format('d-m-Y')
                                : (Carbon::hasFormat($student->dob, 'Y-m-d')
                                    ? Carbon::createFromFormat('Y-m-d', $student->dob)->format('d-m-Y')
                                    : null))
                            : null,

            ];
        });

        return response()->json([
            'schoolcode' => $school->school_code,
            'class' => $request->class,
            'section' => $request->section,
            'studentdetails' => $studentDetails
        ]);

        
    }

}
